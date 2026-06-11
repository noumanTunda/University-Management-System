<?php

use Illuminate\Database\Migrations\Migration;

class FixSubjectSemesterMapping extends Migration
{
    public function up()
    {
        // Map old levelTerm values to new semester names
        $map = [
            'L1T1' => 'Semester 1', 'L1T2' => 'Semester 2',
            'L2T1' => 'Semester 1', 'L2T2' => 'Semester 2',
            'L3T1' => 'Semester 1', 'L3T2' => 'Semester 2',
            'L4T1' => 'Semester 1', 'L4T2' => 'Semester 2',
        ];
        foreach ($map as $old => $new) {
            DB::table('subject')->where('levelTerm', $old)->update(['levelTerm' => $new]);
        }

        // Fix credit column to DECIMAL
        DB::statement('ALTER TABLE subject MODIFY COLUMN credit DECIMAL(5,2) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $map = [
            'Semester 1' => 'L1T1', 'Semester 2' => 'L1T2',
        ];
        foreach ($map as $old => $new) {
            DB::table('subject')->where('levelTerm', $old)->update(['levelTerm' => $new]);
        }
        DB::statement('ALTER TABLE subject MODIFY COLUMN credit VARCHAR(20) NOT NULL');
    }
}
