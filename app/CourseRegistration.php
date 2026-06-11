<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    protected $table = 'course_registrations';
    protected $fillable = [
        'student_id', 'subject_id', 'semester_id',
        'ca_score', 'ue_score', 'grade_letter', 'grade_point', 'status'
    ];

    public function student()
    {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Semester', 'semester_id');
    }

    // TCU Grading: CA max 40, UE max 60
    const CA_MAX = 40;
    const UE_MAX = 60;

    public static function computeGrade($ca, $ue)
    {
        $total = ($ca ?? 0) + ($ue ?? 0);
        if ($total >= 70) return ['letter' => 'A',  'point' => 5.0, 'status' => 'Pass'];
        if ($total >= 60) return ['letter' => 'B+', 'point' => 4.0, 'status' => 'Pass'];
        if ($total >= 50) return ['letter' => 'B',  'point' => 3.0, 'status' => 'Pass'];
        if ($total >= 40) return ['letter' => 'C',  'point' => 2.0, 'status' => 'Pass'];
        if ($total >= 35) return ['letter' => 'D',  'point' => 1.0, 'status' => 'Fail'];
        if ($total >= 20) return ['letter' => 'E',  'point' => 0.5, 'status' => 'Fail'];
        return                ['letter' => 'F',  'point' => 0.0, 'status' => 'Fail'];
    }
}
