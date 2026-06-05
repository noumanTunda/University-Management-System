<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('going to seed user table....');
        $this->call(UserTableSeeder::class);
        $this->command->info('user table seeded.');
        $this->command->info('going to seed admin user...');
        $this->call(AdminUserSeeder::class);
        $this->command->info('admin user seeded.');
        $this->command->info('going to seed institute data...');
        $this->call(InstituteSeeder::class);
        $this->command->info('institute seeded.');

        $this->command->info('going to seed sample data...');
        $this->call(SampleDataSeeder::class);
        $this->command->info('sample data seeded.');

    }
}
