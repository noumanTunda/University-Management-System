<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSchemaNormalization extends Migration
{
    public function up()
    {
        // Fix TZS overflow: fee_collection_items.amount must support up to billions
        DB::statement('ALTER TABLE fee_collection_items MODIFY COLUMN amount DECIMAL(15,2) NOT NULL');

        // Drop physically stored calculated columns from fee_collections
        DB::statement('ALTER TABLE fee_collections DROP COLUMN dueAmount');
        DB::statement('ALTER TABLE fee_collections DROP COLUMN balance');

        // Drop physically stored calculated column from exams
        DB::statement('ALTER TABLE exams DROP COLUMN percentage_x_weight');
    }

    public function down()
    {
        DB::statement('ALTER TABLE fee_collection_items MODIFY COLUMN amount DECIMAL(6,2) NOT NULL');
        DB::statement('ALTER TABLE fee_collections ADD COLUMN dueAmount DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER paidAmount');
        DB::statement('ALTER TABLE fee_collections ADD COLUMN balance DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER dueAmount');
        DB::statement('ALTER TABLE exams ADD COLUMN percentage_x_weight DECIMAL(6,2) NOT NULL DEFAULT 0 AFTER weight');
    }
}
