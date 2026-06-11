<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicCalendarTables extends Migration
{
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 9)->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('academic_year_id')->unsigned();
            $table->tinyInteger('semester_number')->unsigned();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->unique(['academic_year_id', 'semester_number']);
            $table->timestamps();
        });

        // Seed from existing distinct session values in students table
        $sessions = DB::table('students')->distinct()->lists('session');
        foreach ($sessions as $s) {
            if (!empty($s)) {
                DB::table('academic_years')->insert([
                    'name' => $s,
                    'is_active' => 0,
                    'created_at' => Carbon\Carbon::now(),
                    'updated_at' => Carbon\Carbon::now(),
                ]);
            }
        }
        // Activate the latest year
        $latest = DB::table('academic_years')->orderBy('name', 'desc')->first();
        if ($latest) {
            DB::table('academic_years')->where('id', $latest->id)->update(['is_active' => 1]);
        }

        // Seed semesters
        $years = DB::table('academic_years')->get();
        $semNames = ['L1T1','L1T2','L2T1','L2T2','L3T1','L3T2','L4T1','L4T2'];
        foreach ($years as $year) {
            foreach ($semNames as $i => $sem) {
                DB::table('semesters')->insert([
                    'academic_year_id' => $year->id,
                    'semester_number' => $i + 1,
                    'start_date' => null,
                    'end_date' => null,
                    'is_active' => ($year->is_active && $i == 0) ? 1 : 0,
                    'created_at' => Carbon\Carbon::now(),
                    'updated_at' => Carbon\Carbon::now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::drop('semesters');
        Schema::drop('academic_years');
    }
}
