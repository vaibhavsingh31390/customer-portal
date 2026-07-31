<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('test.dummy_password');

        DatabaseSeeder::validatePassword($password);

        foreach (config('test.accounts', []) as $account) {
            DB::table('portal_users')->updateOrInsert(
                ['user_code' => $account['user_code']],
                [
                    'user_code' => $account['user_code'],
                    'username' => $account['username'],
                    'email' => $account['email'],
                    'password' => $password,
                    'status' => 'Y',
                ]
            );
        }

        if ($this->command) {
            $this->command->info('Test users seeded (password: '.config('test.dummy_password').').');
        }
    }
}
