<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentComponent extends Model
{
    protected $table = 'assessment_components';
    protected $fillable = ['assessment_plan_id', 'name', 'type', 'max_score', 'weight'];

    public function plan()
    {
        return $this->belongsTo('App\AssessmentPlan', 'assessment_plan_id');
    }

    public function marks()
    {
        return $this->hasMany('App\AssessmentMark', 'assessment_component_id');
    }
}
