<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Super Admin Account
    |--------------------------------------------------------------------------
    |
    | Credentials used by the SuperAdminUserSeeder to provision the initial
    | super admin account. The password must be provided through the
    | environment; the seeder refuses to run without one.
    |
    */

    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@cars.local'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
    ],

];
