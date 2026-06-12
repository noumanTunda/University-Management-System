<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class PaymentAllocation extends Model {
    protected $table = 'payment_allocations';
    protected $fillable = ['invoice_id', 'receipt_id', 'amount', 'payment_date', 'payment_method', 'reference'];
    protected $dates = ['payment_date'];
}
