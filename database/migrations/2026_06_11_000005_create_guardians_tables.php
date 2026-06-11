<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuardiansTables extends Migration
{
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name', 180);
            $table->string('mobile_no', 15);
            $table->string('alternative_mobile_no', 15)->nullable();
            $table->enum('relationship_type', ['Father', 'Mother', 'Sponsor', 'Other']);
            $table->timestamps();
        });

        Schema::create('guardian_student', function (Blueprint $table) {
            $table->integer('guardian_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->primary(['guardian_id', 'student_id']);
            $table->foreign('guardian_id')->references('id')->on('guardians')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('guardian_student');
        Schema::drop('guardians');
    }
}
