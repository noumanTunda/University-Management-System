<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class FeeInvoice extends Model {
    protected $table = 'fee_invoices';
    protected $fillable = ['student_id', 'invoice_no', 'invoice_date', 'due_date', 'total_amount', 'paid_amount', 'status', 'notes'];
    protected $dates = ['invoice_date', 'due_date'];
    public function student() { return $this->belongsTo('App\Student'); }
    public function items() { return $this->hasMany('App\InvoiceItem', 'invoice_id'); }
}
