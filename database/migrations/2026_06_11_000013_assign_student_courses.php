<?php

use Illuminate\Database\Migrations\Migration;

class AssignStudentCourses extends Migration
{
    public function up()
    {
        // Get the first course per department to assign as default
        $courses = DB::table('courses')
            ->select('department_id', DB::raw('MIN(id) as first_course_id'))
            ->groupBy('department_id')
            ->get();

        $defaultMap = [];
        foreach ($courses as $c) {
            $defaultMap[$c->department_id] = $c->first_course_id;
        }

        $updated = 0;
        $students = DB::table('students')->whereNull('course_id')->whereNull('deleted_at')->get();
        foreach ($students as $s) {
            $courseId = $defaultMap[$s->department_id] ?? null;
            if (!$courseId) continue;
            DB::table('students')->where('id', $s->id)->update([
                'course_id' => $courseId,
                'department_id' => DB::table('courses')->where('id', $courseId)->value('department_id'),
            ]);
            $updated++;
        }

    }

    public function down()
    {
        // Cannot reverse safely
    }
}
