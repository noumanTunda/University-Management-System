<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplateComponent extends Model
{
    protected $table = 'assessment_template_components';
    protected $fillable = ['template_id', 'name', 'type', 'max_score', 'weight'];

    public function template()
    {
        return $this->belongsTo('App\AssessmentTemplate', 'template_id');
    }
}
