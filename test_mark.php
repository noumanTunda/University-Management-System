<?php
// Simulate what getMarkEntry does
$subjectId = 1;
$semesterId = 1;

$plan = \App\AssessmentPlan::where('subject_id', $subjectId)->where('semester_id', $semesterId)->with('components')->first();
echo "Plan: " . ($plan ? "yes (id={$plan->id})" : "no") . "\n";

$defaultComponents = [
    ['id' => 0, 'name' => 'Course Work', 'type' => 'CA', 'max_score' => 40, 'weight' => 40],
    ['id' => 0, 'name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60],
];
$components = $plan ? $plan->components->toArray() : $defaultComponents;
echo "Components: " . count($components) . "\n";
if ($plan) {
    echo "Component IDs: " . json_encode($plan->components->pluck('id')->toArray()) . "\n";
}

$sem = \App\Semester::with('academicYear')->find($semesterId);
if (!$sem) { echo "ERROR: No semester\n"; exit; }
$session = $sem->academicYear ? $sem->academicYear->name : '';
$levelTerm = 'Semester ' . ($sem->semester_number ?? '1');
echo "Session: $session, LevelTerm: $levelTerm\n";

$students = \App\Student::whereHas('registered', function($q) use ($session, $levelTerm) {
    $q->where('session', $session)->where('levelTerm', $levelTerm);
})->orderBy('firstName')->get(['id', 'idNo', 'firstName', 'lastName']);
echo "Students: " . $students->count() . "\n";

$marks = [];
if ($plan) {
    $compIds = $plan->components->pluck('id')->toArray();
    echo "CompIDs: " . json_encode($compIds) . "\n";
    if (!empty($compIds)) {
        $marks = \App\AssessmentMark::whereIn('assessment_component_id', $compIds)
            ->get()
            ->groupBy('student_id');
        echo "Marks groups: " . count($marks) . "\n";
    }
}

$examTypes = \App\ExamType::all();
echo "ExamTypes: " . $examTypes->count() . "\n";

echo "SUCCESS\n";
