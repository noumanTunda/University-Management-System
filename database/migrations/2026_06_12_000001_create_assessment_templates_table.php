<?php

use Illuminate\Database\Migrations\Migration;

class CreateAssessmentTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('assessment_templates', function ($table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->decimal('ca_weight', 5, 2)->default(40);
            $table->decimal('ue_weight', 5, 2)->default(60);
            $table->timestamps();
        });

        Schema::create('assessment_template_components', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->string('name', 100);
            $table->enum('type', ['CA', 'UE']);
            $table->decimal('max_score', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->timestamps();
            $table->foreign('template_id')->references('id')->on('assessment_templates')->onDelete('cascade');
        });

        // Default template: Standard Course
        DB::table('assessment_templates')->insert([
            'name' => 'Standard Course',
            'description' => 'Default template: Course Work (Test 1 + Test 2) 40% + University Exam 60%',
            'ca_weight' => 40,
            'ue_weight' => 60,
        ]);
        $tid = DB::table('assessment_templates')->where('name', 'Standard Course')->first()->id;
        DB::table('assessment_template_components')->insert([
            ['template_id' => $tid, 'name' => 'Test 1', 'type' => 'CA', 'max_score' => 20, 'weight' => 20],
            ['template_id' => $tid, 'name' => 'Test 2', 'type' => 'CA', 'max_score' => 20, 'weight' => 20],
            ['template_id' => $tid, 'name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60],
        ]);
    }

    public function down()
    {
        Schema::drop('assessment_template_components');
        Schema::drop('assessment_templates');
    }
}
