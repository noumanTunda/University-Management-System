<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\Department;
use App\Subject;
use Illuminate\Support\Facades\DB;
use Validator;
use Session;
use Redirect;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
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
        $departments = DB::table('departments')->orderBy('name', 'asc')->pluck('name', 'id');
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
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:4',
            'subjects' => 'array',
            'subjects.*.id' => 'required|exists:subject,id|distinct',
            'subjects.*.semester' => 'required|integer|min:1|max:' . $maxSemester,
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.create')->withInput()->withErrors($validator);
        }

        $course = Course::create(array_only($data, ['name', 'code', 'department_id', 'duration_years']));
        $this->syncSubjects($course, isset($data['subjects']) ? $data['subjects'] : array());

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
        $departments = DB::table('departments')->orderBy('name', 'asc')->pluck('name', 'id');
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
        $rules = [
            'name' => 'required|unique:courses,name,' . $id,
            'code' => 'required|unique:courses,code,' . $id,
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:4',
            'subjects' => 'array',
            'subjects.*.id' => 'required|exists:subject,id|distinct',
            'subjects.*.semester' => 'required|integer|min:1|max:' . $maxSemester,
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.edit', [$id])->withInput()->withErrors($validator);
        }

        $course = Course::findOrFail($id);
        $course->fill(array_only($data, ['name', 'code', 'department_id', 'duration_years']))->save();
        $this->syncSubjects($course, isset($data['subjects']) ? $data['subjects'] : array());

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
            $subjects[] = array(
                'id' => $subject->id,
                'semester' => (int) $subject->pivot->semester,
                'name' => $subject->name,
                'code' => $subject->code,
                'credit' => (float) $subject->credit,
                'department' => $subject->department ? $subject->department->name : 'No Department',
            );
        }

        return $subjects;
    }

    protected function syncSubjects(Course $course, array $subjects)
    {
        $syncData = [];
        $subjectIds = [];
        foreach ($subjects as $subjectData) {
            if (empty($subjectData['id']) || empty($subjectData['semester'])) {
                continue;
            }
            $subjectId = (int) $subjectData['id'];
            $syncData[$subjectId] = ['semester' => (int) $subjectData['semester']];
            $subjectIds[] = $subjectId;
        }
        $course->subjects()->sync($syncData);
        $course->min_credits = empty($subjectIds) ? 0 : (float) Subject::whereIn('id', $subjectIds)->sum('credit');
        $course->save();
    }
}
