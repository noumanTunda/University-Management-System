<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class GePGPaymentReceipt extends Model
{
    protected $table = 'gepg_payment_receipts';
    protected $fillable = ['control_number', 'transaction_id', 'amount_paid', 'payment_provider', 'payer_mobile', 'paid_at'];

    public function bill() { return $this->belongsTo('App\GePGBill', 'control_number', 'control_number'); }
}
