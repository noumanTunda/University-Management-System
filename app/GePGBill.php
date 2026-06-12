<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class GePGBill extends Model
{
    protected $table = 'gepg_bills';
    protected $dates = ['expires_at'];
    protected $fillable = ['student_id', 'fee_collection_id', 'control_number', 'amount', 'bill_description', 'status', 'expires_at'];

    public function student() { return $this->belongsTo('App\Student'); }
    public function receipts() { return $this->hasMany('App\GePGPaymentReceipt', 'control_number', 'control_number'); }
}
