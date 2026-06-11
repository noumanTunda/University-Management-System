<?php

use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class FixSemestersReseed extends Migration
{
    public function up()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        DB::table('semesters')->truncate();
        DB::statement("SET FOREIGN_KEY_CHECKS=1");

        $years = DB::table('academic_years')->get();
        foreach ($years as $year) {
            for ($s = 1; $s <= 2; $s++) {
                DB::table('semesters')->insert([
                    'academic_year_id' => $year->id,
                    'semester_number' => $s,
                    'start_date' => null,
                    'end_date' => null,
                    'is_active' => ($year->is_active && $s == 1) ? 1 : 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    public function down()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        DB::table('semesters')->truncate();
        DB::statement("SET FOREIGN_KEY_CHECKS=1");

        $years = DB::table('academic_years')->get();
        foreach ($years as $year) {
            for ($s = 1; $s <= 8; $s++) {
                DB::table('semesters')->insert([
                    'academic_year_id' => $year->id,
                    'semester_number' => $s,
                    'start_date' => null,
                    'end_date' => null,
                    'is_active' => ($year->is_active && $s == 1) ? 1 : 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
