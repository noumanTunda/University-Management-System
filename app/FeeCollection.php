<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class FeeCollection extends Model
{
    use SoftDeletes;
    protected $table = 'fee_collections';
    protected $fillable = [
        'students_id',
        'payableAmount',
        'lateFee',
        'paidAmount',
        'payDate',
    ];

    protected $appends = ['dueAmount', 'balance'];

    public function getDueAmountAttribute()
    {
        return ($this->payableAmount ?? 0) + ($this->lateFee ?? 0) - ($this->paidAmount ?? 0);
    }

    public function getBalanceAttribute()
    {
        return $this->dueAmount;
    }

    protected $dates = ['created_at','payDate'];

    function setpayDateAttribute($value)
    {
        try {
            $date = Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            $date = Carbon::parse($value);
        }
        $this->attributes['payDate'] = $date;
    }

}
