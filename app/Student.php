<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Student extends Model {
  use SoftDeletes;
  protected $dates = ['created_at','dob'];
  protected $table = 'students';
  protected $fillable = [
    'idNo',
    'session',
    'department_id',
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

}
