<?php
use Illuminate\Database\Seeder;
use App\User;

class AdminUserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run() {
        User::firstOrCreate(
            ['login' => 'admin'],
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'email' => 'admin@test.com',
                // Use the exact group name expected by the authorization gates
                'group' => 'Admin',
                'password' => '!Password', // setPasswordAttribute will bcrypt
            ]
        );
    }
}
