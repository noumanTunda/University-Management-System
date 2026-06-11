<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $table = 'guardians';
    protected $fillable = ['full_name', 'mobile_no', 'alternative_mobile_no', 'relationship_type'];

    public function students()
    {
        return $this->belongsToMany('App\Student', 'guardian_student', 'guardian_id', 'student_id');
    }
}
