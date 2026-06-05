<?php

use Illuminate\Database\Seeder;
use App\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove existing users to avoid duplicates during seeding
        DB::table('users')->delete();
        // Create the admin user with the required credentials
        User::create([
            'firstname' => 'Mr.',
            'lastname'  => 'Admin',
            'login'     => 'admin',
            'email'     => 'admin@test.com',
            // Use the exact group name expected by the authorization gates
            'group'     => 'Admin',
            // The User model hashes the password via setPasswordAttribute
            'password'  => '!Password',
        ]);
    }
}
