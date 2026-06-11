<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssessmentTemplate;
use App\AssessmentTemplateComponent;
use Validator;
use Redirect;

class AssessmentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('hod');
    }

    public function index()
    {
        $templates = AssessmentTemplate::with('components')->orderBy('name')->get();
        return view('assessment_template.index', compact('templates'));
    }

    public function create()
    {
        return view('assessment_template.create');
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|max:150',
            'ca_weight' => 'required|numeric|min:0|max:100',
            'ue_weight' => 'required|numeric|min:0|max:100',
            'components.*.name' => 'required|max:100',
            'components.*.type' => 'required|in:CA,UE',
            'components.*.max_score' => 'required|numeric|min:1',
            'components.*.weight' => 'required|numeric|min:0|max:100',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $template = AssessmentTemplate::create($request->only(['name', 'description', 'ca_weight', 'ue_weight']));

        foreach ($request->input('components', []) as $comp) {
            $template->components()->create($comp);
        }

        return redirect()->route('assessment.template.index')->with('success', ['title'=>'Created','body'=>'Template created.']);
    }

    public function edit($id)
    {
        $template = AssessmentTemplate::with('components')->findOrFail($id);
        return view('assessment_template.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = AssessmentTemplate::findOrFail($id);
        $v = Validator::make($request->all(), [
            'name' => 'required|max:150',
            'ca_weight' => 'required|numeric|min:0|max:100',
            'ue_weight' => 'required|numeric|min:0|max:100',
            'components.*.name' => 'required|max:100',
            'components.*.type' => 'required|in:CA,UE',
            'components.*.max_score' => 'required|numeric|min:1',
            'components.*.weight' => 'required|numeric|min:0|max:100',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $template->update($request->only(['name', 'description', 'ca_weight', 'ue_weight']));
        $template->components()->delete();

        foreach ($request->input('components', []) as $comp) {
            $template->components()->create($comp);
        }

        return redirect()->route('assessment.template.index')->with('success', ['title'=>'Updated','body'=>'Template updated.']);
    }

    public function destroy($id)
    {
        AssessmentTemplate::findOrFail($id)->delete();
        return redirect()->route('assessment.template.index')->with('success', ['title'=>'Deleted','body'=>'Template removed.']);
    }
}
