<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SapModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Sales',
            'Purchase',
            'Inventory',
            'Finance',
            'HR',
            'Production',
        ];

        foreach ($modules as $module) {
            DB::table('sap_modules')->updateOrInsert(
                ['name' => $module, 'department_module' => 'TERMS'],
                ['name' => $module, 'department_module' => 'TERMS']
            );
        }
    }
}
