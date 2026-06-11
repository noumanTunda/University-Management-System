<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacTables extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->primary(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // Seed default roles mapped from existing group values
        DB::table('roles')->insert([
            ['name' => 'Admin',  'description' => 'System Administrator – full access'],
            ['name' => 'Teacher', 'description' => 'Lecturer – can manage attendance, exams, marks'],
            ['name' => 'HeadOfDepartment', 'description' => 'HOD – teacher + subject/course CRUD'],
            ['name' => 'Account', 'description' => 'Bursar/Accountant – fee & accounting modules'],
            ['name' => 'Student', 'description' => 'Student – profile & result viewing'],
        ]);

        // Seed basic permissions
        DB::table('permissions')->insert([
            ['slug' => 'users.manage',    'description' => 'Create/edit/delete users'],
            ['slug' => 'departments.crud', 'description' => 'Manage departments'],
            ['slug' => 'subjects.crud',    'description' => 'Manage subjects'],
            ['slug' => 'courses.crud',     'description' => 'Manage courses'],
            ['slug' => 'students.crud',    'description' => 'Manage students'],
            ['slug' => 'attendance.manage','description' => 'Record & view attendance'],
            ['slug' => 'exams.manage',     'description' => 'Create exams & enter marks'],
            ['slug' => 'results.view',     'description' => 'View examination results'],
            ['slug' => 'fees.manage',      'description' => 'Manage fee structures & collections'],
            ['slug' => 'accounting.view',  'description' => 'View accounting reports'],
            ['slug' => 'library.manage',   'description' => 'Manage library'],
            ['slug' => 'dormitory.manage', 'description' => 'Manage dormitory'],
            ['slug' => 'institute.manage', 'description' => 'Manage institute settings'],
        ]);

        // Map existing users to roles based on their group column
        $users = DB::table('users')->get();
        $roleMap = DB::table('roles')->lists('id', 'name');
        foreach ($users as $user) {
            $roleName = $user->group;
            if (isset($roleMap[$roleName])) {
                DB::table('user_role')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleMap[$roleName],
                ]);
            }
        }
    }

    public function down()
    {
        Schema::drop('user_role');
        Schema::drop('role_permission');
        Schema::drop('permissions');
        Schema::drop('roles');
    }
}
