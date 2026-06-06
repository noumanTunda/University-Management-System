<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Course model representing a study program.
 *
 * A course belongs to a primary department (e.g., CSE) but can contain
 * subjects from any department. Each course has a duration in years and a
 * minimum credit requirement that is calculated from the subjects attached
 * to the course (sum of subject credits across both semesters).
 */
class Course extends Model
{
    use SoftDeletes;

    protected $table = 'courses';
    protected $fillable = [
        'name',
        'code',
        'department_id',
        'duration_years',
        'min_credits',
    ];

    /**
     * Relationship: the primary department that owns the course.
     */
    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    /**
     * Subjects that belong to this course. The pivot table stores the semester
     * (1 or 2) for each subject.
     */
    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'course_subject')
                    ->withPivot('semester')
                    ->orderBy('course_subject.semester')
                    ->withTimestamps();
    }

    /**
     * Registrations (students) that are enrolled in this course.
     */
    public function registrations()
    {
        return $this->hasMany('App\Registration');
    }

    /**
     * Calculate the minimum credits required for the course based on the
     * attached subjects. This is executed before saving the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            // If subjects are already attached, sum their credits.
            if ($model->exists) {
                $credits = $model->subjects()->sum('credit');
                $model->min_credits = $credits;
            }
        });
    }
}
