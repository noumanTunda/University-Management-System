<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentPlan extends Model
{
    protected $table = 'assessment_plans';
    protected $fillable = ['subject_id', 'semester_id', 'ca_weight', 'ue_weight'];

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public function semester()
    {
        return $this->belongsTo('App\Semester');
    }

    public function components()
    {
        return $this->hasMany('App\AssessmentComponent', 'assessment_plan_id');
    }

    public function caComponents()
    {
        return $this->hasMany('App\AssessmentComponent', 'assessment_plan_id')->where('type', 'CA');
    }

    public function ueComponents()
    {
        return $this->hasMany('App\AssessmentComponent', 'assessment_plan_id')->where('type', 'UE');
    }
}
