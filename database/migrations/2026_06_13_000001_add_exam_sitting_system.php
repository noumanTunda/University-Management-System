<?php
use Illuminate\Database\Migrations\Migration;

class AddExamSittingSystem extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Phase 1: Refactor exam_types
        DB::table('exam_types')->truncate();
        DB::statement('ALTER TABLE exam_types MODIFY name VARCHAR(50) NOT NULL');
        DB::table('exam_types')->insert([
            ['id' => 1, 'name' => 'Regular',       'description' => 'Standard end-of-semester sitting (CA + regular UE)'],
            ['id' => 2, 'name' => 'Special',       'description' => 'Special sitting for excused absences (carries over CA)'],
            ['id' => 3, 'name' => 'Supplementary', 'description' => 'Supplementary re-sit for failed courses (capped at C)'],
            ['id' => 4, 'name' => 'Retake',        'description' => 'Full retake — re-register and repeat both CA and UE'],
        ]);

        // Phase 2: Add exam_type_id to assessment_components
        if (!Schema::hasColumn('assessment_components', 'exam_type_id')) {
            Schema::table('assessment_components', function ($table) {
                $table->unsignedInteger('exam_type_id')->nullable()->default(1)->after('assessment_plan_id');
            });
        }
        DB::statement('UPDATE assessment_components SET exam_type_id = 1 WHERE exam_type_id IS NULL');
        DB::statement('ALTER TABLE assessment_components MODIFY exam_type_id INT(10) UNSIGNED NOT NULL DEFAULT 1');

        // Phase 3: Add exam_type_id to assessment_marks
        if (!Schema::hasColumn('assessment_marks', 'exam_type_id')) {
            Schema::table('assessment_marks', function ($table) {
                $table->unsignedInteger('exam_type_id')->nullable()->default(1)->after('student_id');
            });
        }
        DB::statement('UPDATE assessment_marks SET exam_type_id = 1 WHERE exam_type_id IS NULL');
        DB::statement('ALTER TABLE assessment_marks MODIFY exam_type_id INT(10) UNSIGNED NOT NULL DEFAULT 1');

        // Add new unique + index (check if they exist first)
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $idx = $sm->listTableIndexes('assessment_marks');
        if (!array_key_exists('uq_component_student_sitting', $idx)) {
            DB::statement('ALTER TABLE assessment_marks ADD UNIQUE KEY uq_component_student_sitting (assessment_component_id, student_id, exam_type_id)');
            DB::statement('ALTER TABLE assessment_marks ADD INDEX am_component_student_sitting_idx (assessment_component_id, student_id, exam_type_id)');
        }

        // Phase 4: Update course_registrations.status ENUM
        DB::statement("ALTER TABLE course_registrations MODIFY status ENUM('Registered','Incomplete','Pass','Fail','Special','Supp','Retake') NOT NULL DEFAULT 'Registered'");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement("ALTER TABLE course_registrations MODIFY status ENUM('Registered','Incomplete','Pass','Fail') NOT NULL DEFAULT 'Registered'");

        Schema::table('assessment_marks', function ($table) {
            $table->dropIndex('am_component_student_sitting_idx');
            $table->dropUnique('uq_component_student_sitting');
            $table->dropColumn('exam_type_id');
        });

        Schema::table('assessment_components', function ($table) {
            $table->dropColumn('exam_type_id');
        });

        DB::table('exam_types')->truncate();
        DB::table('exam_types')->insert([
            ['name' => 'Course Work'],
            ['name' => 'University Examination'],
            ['name' => 'Testing Exam'],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
