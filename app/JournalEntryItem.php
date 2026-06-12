<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class JournalEntryItem extends Model {
    protected $table = 'journal_entry_items';
    protected $fillable = ['journal_entry_id', 'account_id', 'debit', 'credit'];
}
