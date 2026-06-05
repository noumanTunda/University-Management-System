<?php

use Illuminate\Database\Seeder;
use App\Institute;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure at least one institute record exists
        Institute::firstOrCreate(
            ['name' => 'Default Institute'],
            [
                'establish' => '2000',
                'email'     => 'info@defaultinstitute.com',
                'web'       => 'https://www.defaultinstitute.com',
                'phoneNo'   => '1234567890',
                'address'   => '123 Main St, City, Country',
            ]
        );
    }
}
