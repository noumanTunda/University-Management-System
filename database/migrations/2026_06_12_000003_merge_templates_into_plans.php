<?php

use Illuminate\Database\Migrations\Migration;

class MergeTemplatesIntoPlans extends Migration
{
    public function up()
    {
        // Step 1: Make subject_id and semester_id nullable for template plans
        DB::statement('ALTER TABLE assessment_plans MODIFY subject_id INT UNSIGNED NULL');
        DB::statement('ALTER TABLE assessment_plans MODIFY semester_id INT UNSIGNED NULL');

        // Step 2: Add new columns
        Schema::table('assessment_plans', function ($table) {
            $table->boolean('is_template')->default(false)->after('ue_weight');
            $table->string('description', 255)->nullable()->after('is_template');
            $table->string('template_name', 150)->nullable()->after('description');
        });

        // Step 3: Copy existing templates into assessment_plans
        $oldTemplates = DB::table('assessment_templates')->get();
        foreach ($oldTemplates as $t) {
            $planId = DB::table('assessment_plans')->insertGetId([
                'subject_id' => null,
                'semester_id' => null,
                'ca_weight' => $t->ca_weight,
                'ue_weight' => $t->ue_weight,
                'is_template' => true,
                'description' => $t->description,
                'template_name' => $t->name,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
            // Copy components
            $oldComps = DB::table('assessment_template_components')
                ->where('template_id', $t->id)->get();
            foreach ($oldComps as $c) {
                DB::table('assessment_components')->insert([
                    'assessment_plan_id' => $planId,
                    'name' => $c->name,
                    'type' => $c->type,
                    'max_score' => $c->max_score,
                    'weight' => $c->weight,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
            }
        }

        // Step 4: Drop old template tables
        Schema::dropIfExists('assessment_template_components');
        Schema::dropIfExists('assessment_templates');
    }

    public function down()
    {
        // Recreate template tables
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

        // Move template plans back
        $tplPlans = DB::table('assessment_plans')->where('is_template', true)->get();
        foreach ($tplPlans as $p) {
            $tid = DB::table('assessment_templates')->insertGetId([
                'name' => $p->template_name ?? 'Migrated Template',
                'description' => $p->description,
                'ca_weight' => $p->ca_weight,
                'ue_weight' => $p->ue_weight,
            ]);
            $comps = DB::table('assessment_components')->where('assessment_plan_id', $p->id)->get();
            foreach ($comps as $c) {
                DB::table('assessment_template_components')->insert([
                    'template_id' => $tid,
                    'name' => $c->name,
                    'type' => $c->type,
                    'max_score' => $c->max_score,
                    'weight' => $c->weight,
                ]);
            }
            DB::table('assessment_plans')->where('id', $p->id)->delete();
        }

        Schema::table('assessment_plans', function ($table) {
            $table->dropColumn(['is_template', 'description', 'template_name']);
        });
    }
}
