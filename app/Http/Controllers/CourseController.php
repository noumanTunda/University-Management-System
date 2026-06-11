<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\Department;
use App\Subject;
use Validator;
use Session;
use Redirect;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('hod');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::with('department')->get();
        return view('course.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::orderBy('name', 'asc')->pluck('name', 'id');
        $subjectCatalog = $this->subjectPool();
        $initialAssignments = old('subjects', []);

        return view('course.create', compact('departments', 'subjectCatalog', 'initialAssignments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $maxSemester = max(2, ((int) $request->input('duration_years', 4)) * 2);
        $rules = [
            'name' => 'required|unique:courses',
            'code' => 'required|unique:courses',
            'department_id' => 'required|exists:department,id',
            'duration_years' => 'required|integer|min:1|max:4',
            'subjects' => 'required|array',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.create')->withInput()->withErrors($validator);
        }

        $course = Course::create(array_only($data, ['name', 'code', 'department_id', 'duration_years']));
        $this->syncSubjects($course, isset($data['subjects']) ? $data['subjects'] : array(), (int) $data['duration_years']);

        $notification = array('title' => 'Data Store', 'body' => 'Course created successfully.');
        return Redirect::route('course.index')->with('success', $notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with(['department', 'subjects'])->findOrFail($id);
        return view('course.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::with('subjects')->findOrFail($id);
        $departments = Department::orderBy('name', 'asc')->pluck('name', 'id');
        $subjectCatalog = $this->subjectPool();
        $initialAssignments = old('subjects', $this->selectedSubjects($course));

        return view('course.edit', compact('course', 'departments', 'subjectCatalog', 'initialAssignments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $maxSemester = max(2, ((int) $request->input('duration_years', 4)) * 2);
        $course = Course::with('subjects')->findOrFail($id);
        $rules = [
            'name' => 'required|unique:courses,name,' . $id,
            'code' => 'required|unique:courses,code,' . $id,
            'department_id' => 'required|exists:department,id',
            'duration_years' => 'required|integer|min:1|max:4',
            'subjects' => 'array',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.edit', [$id])->withInput()->withErrors($validator);
        }

        $course->fill(array_only($data, ['name', 'code', 'department_id', 'duration_years']))->save();
        if (isset($data['subjects']) && is_array($data['subjects'])) {
            $this->syncSubjects($course, $data['subjects'], (int) $data['duration_years']);
        }

        $notification = array('title' => 'Data Update', 'body' => 'Course updated successfully.');
        return Redirect::route('course.index')->with('success', $notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        $notification = array('title' => 'Data Delete', 'body' => 'Course deleted successfully.');
        return Redirect::route('course.index')->with('success', $notification);
    }

    protected function subjectPool()
    {
        $subjects = array();
        foreach (Subject::with('department')->orderBy('name', 'asc')->get() as $subject) {
            $subjects[] = array(
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'credit' => (float) $subject->credit,
                'department' => $subject->department ? $subject->department->name : 'No Department',
            );
        }

        return $subjects;
    }

    protected function selectedSubjects(Course $course)
    {
        $subjects = array();
        foreach ($course->subjects as $subject) {
            $semester = (int) $subject->pivot->semester;
            $year = (int) ceil($semester / 2);
            $semesterInYear = $semester % 2 === 0 ? 2 : 1;
            if (!isset($subjects[$year])) {
                $subjects[$year] = array();
            }
            if (!isset($subjects[$year][$semesterInYear])) {
                $subjects[$year][$semesterInYear] = array();
            }
            $subjects[$year][$semesterInYear][$subject->id] = array('selected' => 1);
        }

        return $subjects;
    }

    protected function syncSubjects(Course $course, array $subjects, $durationYears)
    {
        $syncData = [];
        $subjectIds = [];
        foreach ($subjects as $year => $semesters) {
            if (!is_array($semesters)) {
                continue;
            }
            if ((int) $year > (int) $durationYears) {
                continue;
            }
            foreach ($semesters as $semesterInYear => $subjectList) {
                if (!is_array($subjectList)) {
                    continue;
                }
                $semester = ((int) $year - 1) * 2 + (int) $semesterInYear;
                foreach ($subjectList as $subjectId => $subjectData) {
                    if (empty($subjectData['selected'])) {
                        continue;
                    }
                    $subjectId = (int) $subjectId;
                    $syncData[$subjectId] = array('semester' => $semester);
                    $subjectIds[] = $subjectId;
                }
            }
        }
        $course->subjects()->sync($syncData);
        $course->min_credits = empty($subjectIds) ? 0 : (float) Subject::whereIn('id', $subjectIds)->sum('credit');
        $course->save();
    }
}
