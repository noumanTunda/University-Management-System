<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('student_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('semester_id')->unsigned();
            $table->decimal('ca_score', 5, 2)->nullable();
            $table->decimal('ue_score', 5, 2)->nullable();
            $table->string('grade_letter', 2)->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->enum('status', ['Registered', 'Incomplete', 'Pass', 'Fail', 'Supp', 'Retake'])->default('Registered');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->unique(['student_id', 'subject_id', 'semester_id'], 'uq_student_course_semester');
        });

        // Add computed final_mark column (MySQL 8.0 generated column)
        DB::statement('ALTER TABLE course_registrations ADD COLUMN final_mark DECIMAL(5,2) GENERATED ALWAYS AS (COALESCE(ca_score, 0) + COALESCE(ue_score, 0)) STORED AFTER ue_score');
    }

    public function down()
    {
        Schema::drop('course_registrations');
    }
}
