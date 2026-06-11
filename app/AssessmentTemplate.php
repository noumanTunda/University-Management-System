<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplate extends Model
{
    protected $table = 'assessment_templates';
    protected $fillable = ['name', 'description', 'ca_weight', 'ue_weight'];

    public function components()
    {
        return $this->hasMany('App\AssessmentTemplateComponent', 'template_id');
    }
}
