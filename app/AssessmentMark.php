<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentMark extends Model
{
    protected $table = 'assessment_marks';
    protected $fillable = ['assessment_component_id', 'student_id', 'score'];

    public function component()
    {
        return $this->belongsTo('App\AssessmentComponent', 'assessment_component_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Student');
    }
}
