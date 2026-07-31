<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionUserSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->command && ! $this->command->getLaravel()->runningUnitTests()) {
            $this->seedInteractively();

            return;
        }

        $this->seedAdmin(
            env('DB_ADMIN_USERNAME', 'admin'),
            env('DB_ADMIN_EMAIL', 'admin@example.com'),
            env('DB_ADMIN_PASSWORD', 'Password@123'),
            env('DB_ADMIN_NAME', 'Portal Administrator'),
            env('DB_ADMIN_USER_CODE', 'S000'),
        );
    }

    protected function seedInteractively(): void
    {
        $username = $this->command->ask('Admin username', 'admin');
        $email = $this->command->ask('Admin email', 'admin@example.com');
        $name = $this->command->ask('Admin display name', 'Portal Administrator');
        $userCode = $this->command->ask('Admin user code (support users start with S)', 'S000');

        do {
            $password = $this->command->secret(
                'Admin password (min 8 chars, upper, lower, number, symbol)'
            );

            if ($password === null || $password === '') {
                $this->command->error('Password is required.');

                continue;
            }

            try {
                DatabaseSeeder::validatePassword($password);
                break;
            } catch (\InvalidArgumentException $e) {
                $this->command->error($e->getMessage());
                $password = null;
            }
        } while (true);

        $this->seedAdmin($username, $email, $password, $name, $userCode);
        $this->command->info("Admin user seeded: {$username}");
    }

    protected function seedAdmin(
        string $username,
        string $email,
        string $password,
        string $name,
        string $userCode,
    ): void {
        DatabaseSeeder::validatePassword($password);

        DB::table('engineers')->updateOrInsert(
            ['engineer_code' => $userCode],
            [
                'engineer_code' => $userCode,
                'name' => $name,
                'working_status' => 'WK',
                'department' => 'SWE',
            ]
        );

        DB::table('portal_users')->updateOrInsert(
            ['user_code' => $userCode],
            [
                'user_code' => $userCode,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'status' => 'Y',
            ]
        );
    }
}
