<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamTypesTable extends Migration
{
    public function up()
    {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        DB::table('exam_types')->insert([
            ['name' => 'Midterm Exam', 'description' => 'Mid-term examination'],
            ['name' => 'Final Exam', 'description' => 'Final examination'],
            ['name' => 'Quiz', 'description' => 'Class quiz / test'],
            ['name' => 'Assignment', 'description' => 'Course assignment'],
            ['name' => 'Practical', 'description' => 'Practical / lab exam'],
        ]);
    }

    public function down()
    {
        Schema::drop('exam_types');
    }
}
