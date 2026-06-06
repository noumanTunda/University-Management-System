<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceToFeeCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            // Balance reflects the remaining amount to be paid (negative when over‑paid)
            $table->decimal('balance', 15, 2)->default(0)->after('dueAmount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
}
