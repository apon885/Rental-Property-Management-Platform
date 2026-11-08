<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The only way to get an admin account: seeder or `php artisan admin:create`.
        User::updateOrCreate(
            ['email' => 'admin@rental.test'],
            [
                'name'                => 'System Admin',
                'phone'               => '01700000000',
                'password'            => Hash::make('Admin@12345'),
                'role'                => 'admin',
                'status'              => 'active',
                'verification_status' => 'verified',
            ]
        );
    }
}
