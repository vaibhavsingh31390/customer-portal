<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! $this->command) {
            if (config('test.enabled')) {
                $this->callTestSeeders();
            } else {
                $this->callProductionSeeders();
            }

            return;
        }

        $forTesting = $this->command->confirm(
            'Is this seed for testing? (Yes = dummy data + test panel users, No = basic setup + admin user)',
            false
        );

        if ($forTesting) {
            $this->command->info('Seeding test/dummy data…');
            $this->callTestSeeders();
        } else {
            $this->command->info('Seeding production baseline (modules + admin user)…');
            $this->callProductionSeeders();
        }
    }

    protected function callTestSeeders(): void
    {
        $this->call([
            CelintMasterSeeder::class,
            EngMasterSeeder::class,
            SapModuleSeeder::class,
            TestUserSeeder::class,
            CustomerComplaintSeeder::class,
        ]);
    }

    protected function callProductionSeeders(): void
    {
        $this->call([
            SapModuleSeeder::class,
            ProductionUserSeeder::class,
        ]);
    }

    public static function passwordRules(): array
    {
        return ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()];
    }

    public static function validatePassword(string $password, string $field = 'password'): void
    {
        $validator = Validator::make(
            [$field => $password],
            [$field => self::passwordRules()]
        );

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first($field));
        }
    }
}
