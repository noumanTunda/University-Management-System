<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentComponent extends Model
{
    protected $table = 'assessment_components';
    protected $fillable = ['assessment_plan_id', 'exam_type_id', 'name', 'type', 'max_score', 'weight'];

    public function plan()
    {
        return $this->belongsTo('App\AssessmentPlan', 'assessment_plan_id');
    }

    public function examType()
    {
        return $this->belongsTo('App\ExamType');
    }

    public function marks()
    {
        return $this->hasMany('App\AssessmentMark', 'assessment_component_id');
    }
}
