<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExamType;
use Validator;
use Redirect;

class ExamTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('hod');
    }

    public function index()
    {
        $examTypes = ExamType::all();
        return view('exam_type.index', compact('examTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $rules = ['name' => 'required|max:100|unique:exam_types'];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        ExamType::create($data);
        $notification = ['title' => 'Data Store', 'body' => 'Exam type created successfully.'];
        return redirect()->route('exam_type.index')->with('success', $notification);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $rules = ['name' => 'required|max:100|unique:exam_types,name,' . $id];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $examType = ExamType::findOrFail($id);
        $examType->fill($data)->save();
        $notification = ['title' => 'Data Update', 'body' => 'Exam type updated.'];
        return redirect()->route('exam_type.index')->with('success', $notification);
    }

    public function destroy($id)
    {
        ExamType::findOrFail($id)->delete();
        $notification = ['title' => 'Data Delete', 'body' => 'Exam type deleted.'];
        return redirect()->route('exam_type.index')->with('success', $notification);
    }
}
