<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EngMasterSeeder extends Seeder
{
    public function run(): void
    {
        $engineers = [
            ['engineer_code' => 'S001', 'name' => 'Rahul Sharma', 'working_status' => 'WK', 'department' => 'SWE'],
            ['engineer_code' => 'S002', 'name' => 'Priya Patel', 'working_status' => 'WK', 'department' => 'SWE'],
            ['engineer_code' => 'S003', 'name' => 'Amit Kumar', 'working_status' => 'WK', 'department' => 'SWE'],
        ];

        foreach ($engineers as $engineer) {
            DB::table('engineers')->updateOrInsert(
                ['engineer_code' => $engineer['engineer_code']],
                $engineer
            );
        }
    }
}
