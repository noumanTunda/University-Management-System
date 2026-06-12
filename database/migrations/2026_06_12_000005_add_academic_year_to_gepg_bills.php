<?php

use Illuminate\Database\Migrations\Migration;

class AddAcademicYearToGePgBills extends Migration
{
    public function up()
    {
        Schema::table('gepg_bills', function ($table) {
            $table->string('academic_year', 20)->nullable()->after('expires_at');
        });
    }

    public function down()
    {
        Schema::table('gepg_bills', function ($table) {
            $table->dropColumn('academic_year');
        });
    }
}
