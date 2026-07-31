<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CelintMasterSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['client_code' => 'C001', 'name' => 'Acme Logistics', 'erp_vertical' => 'TERMS', 'email' => 'acme@example.com'],
            ['client_code' => 'C002', 'name' => 'Bright Retail', 'erp_vertical' => 'TERMS', 'email' => 'bright@example.com'],
            ['client_code' => 'C003', 'name' => 'Delta Manufacturing', 'erp_vertical' => 'TERMS', 'email' => 'delta@example.com'],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->updateOrInsert(
                ['client_code' => $client['client_code']],
                $client
            );
        }
    }
}
