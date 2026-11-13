<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Creates the CMS admin login. Content and schema live in the nonstop_backend repo. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@nonstop.test')],
            ['name' => 'Admin', 'password' => Hash::make(env('ADMIN_PASSWORD', 'change-me-now'))]
        );
    }
}
