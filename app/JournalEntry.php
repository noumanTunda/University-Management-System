<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class JournalEntry extends Model {
    protected $table = 'journal_entries';
    protected $fillable = ['entry_date', 'description', 'reference_type', 'reference_id'];
    protected $dates = ['entry_date'];
    public function items() { return $this->hasMany('App\JournalEntryItem', 'journal_entry_id'); }
}
