<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Course;

class Student extends Model {
  use SoftDeletes;
  protected $dates = ['created_at','dob'];
  protected $table = 'students';
  protected $fillable = [
    'idNo',
    'session',
    'course_id',
    'bncReg',
    'batchNo',
    'firstName',
    'middleName',
    'lastName',
    'mobileNo',
    'gender',
    'religion',
    'bloodgroup',
    'nationality',
    'dob',
    'photo',
    'fatherName',
    'fatherMobileNo',
    'motherName',
    'motherMobileNo',
    'localGuardian',
    'localGuardianMobileNo',
    'presentAddress',
    'parmanentAddress',
    'isActive'
  ];

  protected $guarded = ['department_id'];
  public static function boot()
  {
    parent::boot();
    static::deleting(function($student) {
      $student->registered()->delete();
      $student->attendance()->delete();
      $student->exams()->delete();
      $student->feeCollections()->delete();
    });
  }
  /**
   * Mutator for the `dob` attribute.
   *
   * The original implementation expected the date in the format
   * `d/m/Y` (e.g., 31/12/1999). When importing students via CSV we store
   * the date already formatted as `Y-m-d` (e.g., 1999-12-31). To keep the
   * existing form behaviour while supporting the CSV import, the mutator
   * now attempts to parse both formats. If the value matches the ISO
   * format (`Y-m-d`) it is stored directly; otherwise we fall back to the
   * original `d/m/Y` format. Invalid dates are ignored so the model does
   * not throw an exception.
   */
  function setDobAttribute($value)
  {
    if (empty($value)) {
        $this->attributes['dob'] = null;
        return;
    }
    // Try ISO format first (Y-m-d)
    try {
        $date = Carbon::createFromFormat('Y-m-d', $value);
        $this->attributes['dob'] = $date;
        return;
    } catch (\Exception $e) {
        // fall back to original format
    }
    try {
        $date = Carbon::createFromFormat('d/m/Y', $value);
        $this->attributes['dob'] = $date;
    } catch (\Exception $e) {
        // If parsing fails, store null to avoid breaking the model
        $this->attributes['dob'] = null;
    }
  }
  public function department() {
    return $this->belongsTo('App\Department');
  }

  public function course()
  {
      return $this->belongsTo('App\Course');
  }

  public function setCourseIdAttribute($value)
  {
      $this->attributes['course_id'] = $value;
      if ($value) {
          $course = Course::find($value);
          $this->attributes['department_id'] = $course ? $course->department_id : null;
      }
  }
  public function registered() {
    return $this->hasMany('App\Registration','students_id');
  }
  public function attendance() {
    return $this->hasMany('App\Attendance','students_id');
  }
  public function exams() {
    return $this->hasMany('App\Exam','students_id');
  }
  public function feeCollections() {
    return $this->hasMany('App\FeeCollection','students_id');
  }
  public function guardians() {
    return $this->belongsToMany('App\Guardian', 'guardian_student', 'student_id', 'guardian_id');
  }

}
