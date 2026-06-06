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
        'dueAmount',
        'payDate'
    ];
    protected $dates = ['created_at','payDate'];
    /**
     * Mutator for the payDate attribute.
     *
     * The original implementation expected a date string in the
     * "d/m/Y" format (e.g., 31/12/2023). However, the HTML <input type="date">
     * element used in the payment form sends dates as "Y-m-d" (e.g., 2023-12-31).
     * This caused a Carbon::createFromFormat error when the form submitted
     * a value like "2026-06-06".
     *
     * To make the model robust, we now attempt to parse the incoming value
     * using Carbon::createFromFormat for the known "d/m/Y" format first. If that
     * fails, we fall back to Carbon::parse which can handle ISO dates and many
     * other common formats.
     */
    function setpayDateAttribute($value)
    {
        try {
            // Try the original format first.
            $date = Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            // Fallback to a more flexible parser (handles Y-m-d, etc.).
            $date = Carbon::parse($value);
        }
        $this->attributes['payDate'] = $date;
    }

}
