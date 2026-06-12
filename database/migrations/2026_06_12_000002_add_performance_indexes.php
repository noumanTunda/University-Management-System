<?php

use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // E8: Database indexing for frequently queried columns
        try { DB::statement('ALTER TABLE students ADD INDEX students_idno_index (idNo(20))'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE students ADD INDEX students_department_id_index (department_id)'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE students ADD INDEX students_session_index (`session`(15))'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE registrations ADD INDEX registrations_students_id_index (students_id)'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE registrations ADD INDEX registrations_session_index (`session`(15))'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE registrations ADD INDEX registrations_level_term_index (levelTerm(20))'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE attendances ADD INDEX attendances_students_id_index (students_id)'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE attendances ADD INDEX attendances_subject_id_index (subject_id)'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE attendances ADD INDEX attendances_date_index (date)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE exams ADD INDEX exams_students_id_index (students_id)'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE exams ADD INDEX exams_subject_id_index (subject_id)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE fee_collections ADD INDEX fee_collections_students_id_index (students_id)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE course_registrations ADD INDEX cr_student_subject_index (student_id, subject_id)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE assessment_marks ADD INDEX am_component_student_index (assessment_component_id, student_id)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE users ADD INDEX users_login_index (login(50))'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE users ADD INDEX users_group_index (`group`(20))'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE subject ADD INDEX subject_department_id_index (department_id)'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE course_subject ADD INDEX cs_course_subject_index (course_id, subject_id)'); } catch (\Exception $e) {}
    }

    public function down()
    {
        // Drop indexes — too many to list individually, kept intentionally
    }
}
