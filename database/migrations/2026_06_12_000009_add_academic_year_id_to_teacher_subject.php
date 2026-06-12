<?php
use Illuminate\Database\Migrations\Migration;

class AddAcademicYearIdToTeacherSubject extends Migration
{
    public function up()
    {
        Schema::table('teacher_subject', function ($table) {
            $table->unsignedInteger('academic_year_id')->nullable()->after('subject_id');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });

        // Backfill: copy academic_year string to academic_year_id
        $rows = DB::table('teacher_subject')->whereNull('academic_year_id')->get();
        foreach ($rows as $row) {
            $yearName = $row->academic_year;
            if ($yearName) {
                $year = DB::table('academic_years')->where('name', $yearName)->first();
                if ($year) {
                    DB::table('teacher_subject')->where('id', $row->id)->update(['academic_year_id' => $year->id]);
                }
            }
        }
    }

    public function down()
    {
        Schema::table('teacher_subject', function ($table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
}
