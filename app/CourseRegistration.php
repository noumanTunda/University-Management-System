<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    protected $table = 'course_registrations';
    protected $fillable = ['student_id', 'subject_id', 'semester_id', 'ca_score', 'ue_score',
                           'final_mark', 'grade_letter', 'grade_point', 'status', 'exam_type_id'];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public function semester()
    {
        return $this->belongsTo('App\Semester');
    }

    const CA_MAX = 40;
    const UE_MAX = 60;

    /**
     * Compute grade based on CA and UE scores and sitting type.
     *
     * @param float $ca  Continuous Assessment score (out of 40)
     * @param float $ue  University Examination score (out of 60)
     * @param int|null $examTypeId  1=Regular, 2=Special, 3=Supplementary, 4=Retake
     * @return array  ['letter', 'point', 'status']
     */
    public static function computeGrade($ca, $ue, $examTypeId = 1)
    {
        $ca = (float)($ca ?? 0);
        $ue = (float)($ue ?? 0);
        $total = $ca + $ue;

        // Regular & Special — no cap
        if ($examTypeId == 1 || $examTypeId == 2) {
            if ($total >= 70) return ['letter' => 'A',  'point' => 5.0, 'status' => 'Pass'];
            if ($total >= 60) return ['letter' => 'B+', 'point' => 4.0, 'status' => 'Pass'];
            if ($total >= 50) return ['letter' => 'B',  'point' => 3.0, 'status' => 'Pass'];
            if ($total >= 40) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
            if ($total >= 35) return ['letter' => 'D',  'point' => 1.0, 'status' => 'Fail'];
            if ($total >= 20) return ['letter' => 'E',  'point' => 0.5, 'status' => 'Fail'];
            return                ['letter' => 'F',  'point' => 0.0, 'status' => 'Fail'];
        }

        // Supplementary & Retake — capped at C (2.0)
        if ($examTypeId == 3 || $examTypeId == 4) {
            if ($total >= 70) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
            if ($total >= 60) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
            if ($total >= 50) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
            if ($total >= 40) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
            if ($total >= 35) return ['letter' => 'D',  'point' => 1.0, 'status' => 'Fail'];
            if ($total >= 20) return ['letter' => 'E',  'point' => 0.5, 'status' => 'Fail'];
            return                ['letter' => 'F',  'point' => 0.0, 'status' => 'Fail'];
        }

        // Fallback — treat as Regular
        if ($total >= 70) return ['letter' => 'A',  'point' => 5.0, 'status' => 'Pass'];
        if ($total >= 60) return ['letter' => 'B+', 'point' => 4.0, 'status' => 'Pass'];
        if ($total >= 50) return ['letter' => 'B',  'point' => 3.0, 'status' => 'Pass'];
        if ($total >= 40) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
        if ($total >= 35) return ['letter' => 'D',  'point' => 1.0, 'status' => 'Fail'];
        if ($total >= 20) return ['letter' => 'E',  'point' => 0.5, 'status' => 'Fail'];
        return                ['letter' => 'F',  'point' => 0.0, 'status' => 'Fail'];
    }

    /**
     * Compute grade for a student by fetching marks from appropriate sittings.
     *
     * @param int $studentId
     * @param int $subjectId
     * @param int $semesterId
     * @param int $examTypeId  1=Regular, 2=Special, 3=Supplementary
     * @return array  ['letter', 'point', 'status', 'ca', 'ue']
     */
    public static function computeGradeFromMarks($studentId, $subjectId, $semesterId, $examTypeId = 1)
    {
        // Get the plan for this subject+semester
        $plan = AssessmentPlan::where('subject_id', $subjectId)
            ->where('semester_id', $semesterId)
            ->with('components')
            ->first();

        if (!$plan) {
            return ['letter' => 'N/A', 'point' => 0, 'status' => 'Registered', 'ca' => 0, 'ue' => 0];
        }

        $caTotal = 0;
        $ueTotal = 0;

        foreach ($plan->components as $comp) {
            $mark = AssessmentMark::where('assessment_component_id', $comp->id)
                ->where('student_id', $studentId)
                ->where('exam_type_id', $examTypeId)
                ->first();

            $score = $mark ? (float)$mark->score : 0;

            if ($comp->type === 'CA') {
                $caTotal += $score;
            } else {
                $ueTotal += $score;
            }
        }

        $grade = self::computeGrade($caTotal, $ueTotal, $examTypeId);

        $grade['ca'] = $caTotal;
        $grade['ue'] = $ueTotal;

        return $grade;
    }
}
