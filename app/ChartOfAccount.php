<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class ChartOfAccount extends Model {
    protected $table = 'chart_of_accounts';
    protected $fillable = ['code', 'name', 'type', 'balance', 'description'];
}
