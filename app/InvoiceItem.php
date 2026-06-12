<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class InvoiceItem extends Model {
    protected $table = 'invoice_items';
    protected $fillable = ['invoice_id', 'description', 'amount', 'fee_id', 'account_id'];
}
