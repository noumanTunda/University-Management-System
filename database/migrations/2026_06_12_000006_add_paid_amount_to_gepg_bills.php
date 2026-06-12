<?php

use Illuminate\Database\Migrations\Migration;

class AddPaidAmountToGePgBills extends Migration
{
    public function up()
    {
        Schema::table('gepg_bills', function ($table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('amount');
        });
    }

    public function down()
    {
        Schema::table('gepg_bills', function ($table) {
            $table->dropColumn('paid_amount');
        });
    }
}
