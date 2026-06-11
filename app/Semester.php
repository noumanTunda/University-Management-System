<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semesters';
    protected $fillable = ['academic_year_id', 'semester_number', 'start_date', 'end_date', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'start_date' => 'date', 'end_date' => 'date'];

    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear', 'academic_year_id');
    }
}
