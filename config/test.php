<?php

return [

    'enabled' => filter_var(env('TEST_MODE', false), FILTER_VALIDATE_BOOL),

    /** Shared dummy password for all test-panel accounts (strict-friendly). */
    'dummy_password' => 'Password@123',

    /**
     * Canonical test accounts — used by the login test panel AND TestUserSeeder.
     * Password is always config('test.dummy_password').
     */
    'accounts' => [
        [
            'username' => 'testadmin',
            'user_code' => 'A000',
            'email' => 'admin@heyvai.dev',
            'label' => 'Admin',
            'description' => 'Portal administrator — manage users & clients',
            'role' => 'admin',
        ],
        [
            'username' => 'client1',
            'user_code' => 'C001',
            'email' => 'client1@acme.com',
            'label' => 'Client 1',
            'description' => 'Acme Logistics',
            'role' => 'client',
        ],
        [
            'username' => 'client2',
            'user_code' => 'C002',
            'email' => 'client2@bright.com',
            'label' => 'Client 2',
            'description' => 'Bright Retail',
            'role' => 'client',
        ],
        [
            'username' => 'support1',
            'user_code' => 'S001',
            'email' => 'support1@heyvai.dev',
            'label' => 'Support 1',
            'description' => 'Rahul Sharma',
            'role' => 'support',
        ],
        [
            'username' => 'support2',
            'user_code' => 'S002',
            'email' => 'support2@heyvai.dev',
            'label' => 'Support 2',
            'description' => 'Priya Patel',
            'role' => 'support',
        ],
    ],

];
