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
        $departments = Department::pluck('name', 'id');
        $subjects = Subject::pluck('name', 'id');
        return view('course.create', compact('departments', 'subjects'));
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
        $rules = [
            'name' => 'required|unique:courses',
            'code' => 'required|unique:courses',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1',
            'subjects' => 'array',
            'subjects.*.id' => 'exists:subject,id',
            'subjects.*.semester' => 'required|integer|min:1|max:2',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.create')->withInput()->withErrors($validator);
        }

        $course = Course::create(array_only($data, ['name', 'code', 'department_id', 'duration_years']));

        $syncData = [];
        if (isset($data['subjects'])) {
            foreach ($data['subjects'] as $subjectData) {
                $syncData[$subjectData['id']] = ['semester' => $subjectData['semester']];
            }
        }

        if (!empty($syncData)) {
            $course->subjects()->sync($syncData);
        }

        $course->min_credits = Subject::whereIn('id', array_keys($syncData))->sum('credit');
        $course->save();

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
        $departments = Department::pluck('name', 'id');
        $allSubjects = Subject::pluck('name', 'id');
        $selectedSubjects = $course->subjects->keyBy('id')->map(function ($item) {
            return ['semester' => $item->pivot->semester];
        })->toArray();

        return view('course.edit', compact('course', 'departments', 'allSubjects', 'selectedSubjects'));
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
        $rules = [
            'name' => 'required|unique:courses,name,' . $id,
            'code' => 'required|unique:courses,code,' . $id,
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1',
            'subjects' => 'array',
            'subjects.*.id' => 'exists:subject,id',
            'subjects.*.semester' => 'required|integer|min:1|max:2',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return Redirect::route('course.edit', [$id])->withInput()->withErrors($validator);
        }

        $course = Course::findOrFail($id);
        $course->fill(array_only($data, ['name', 'code', 'department_id', 'duration_years']))->save();

        // Sync subjects and their pivot data
        $syncData = [];
        if (isset($data['subjects'])) {
            foreach ($data['subjects'] as $subjectData) {
                $syncData[$subjectData['id']] = ['semester' => $subjectData['semester']];
            }
        }
        $course->subjects()->sync($syncData);
        $course->min_credits = Subject::whereIn('id', array_keys($syncData))->sum('credit');
        $course->save();

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
}
