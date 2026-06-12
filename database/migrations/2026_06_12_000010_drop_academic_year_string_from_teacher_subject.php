<?php
use Illuminate\Database\Migrations\Migration;

class DropAcademicYearStringFromTeacherSubject extends Migration
{
    public function up()
    {
        Schema::table('teacher_subject', function ($table) {
            $table->dropColumn('academic_year');
        });
    }

    public function down()
    {
        Schema::table('teacher_subject', function ($table) {
            $table->string('academic_year', 20)->nullable()->after('subject_id');
        });
    }
}
