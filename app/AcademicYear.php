<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'academic_years';
    protected $fillable = ['name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function semesters()
    {
        return $this->hasMany('App\Semester', 'academic_year_id');
    }
}
