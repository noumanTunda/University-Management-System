<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentStructure extends Migration
{
    public function up()
    {
        // One plan per subject+semester
        Schema::create('assessment_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subject_id')->unsigned();
            $table->integer('semester_id')->unsigned();
            $table->decimal('ca_weight', 5, 2)->default(40);
            $table->decimal('ue_weight', 5, 2)->default(60);
            $table->timestamps();
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->unique(['subject_id', 'semester_id']);
        });

        // Individual components within a plan (e.g., Quiz 1, Assignment, Practical, Final Exam)
        Schema::create('assessment_components', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessment_plan_id')->unsigned();
            $table->string('name', 100);
            $table->enum('type', ['CA', 'UE']);
            $table->decimal('max_score', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->timestamps();
            $table->foreign('assessment_plan_id')->references('id')->on('assessment_plans')->onDelete('cascade');
        });

        // Per-student marks for each component
        Schema::create('assessment_marks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessment_component_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
            $table->foreign('assessment_component_id')->references('id')->on('assessment_components')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['assessment_component_id', 'student_id'], 'uq_component_student');
        });
    }

    public function down()
    {
        Schema::drop('assessment_marks');
        Schema::drop('assessment_components');
        Schema::drop('assessment_plans');
    }
}
