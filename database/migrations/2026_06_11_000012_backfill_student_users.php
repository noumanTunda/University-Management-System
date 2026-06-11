<?php

use Illuminate\Database\Migrations\Migration;

class BackfillStudentUsers extends Migration
{
    public function up()
    {
        $students = DB::table('students')->whereNull('deleted_at')->get();
        $role = DB::table('roles')->where('name', 'Student')->first();
        $count = 0;
        foreach ($students as $s) {
            $exists = DB::table('users')->where('login', $s->idNo)->first();
            if ($exists) continue;
            DB::table('users')->insert([
                'firstname' => $s->firstName,
                'lastname' => $s->lastName,
                'login' => $s->idNo,
                'password' => bcrypt($s->lastName),
                'group' => 'Student',
                'email' => $s->idNo . '@student.osums.edu',
            ]);
            if ($role) {
                $user = DB::table('users')->where('login', $s->idNo)->first();
                DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => $role->id]);
            }
            $count++;
        }
    }

    public function down()
    {
        DB::table('users')->where('group', 'Student')->delete();
    }
}
