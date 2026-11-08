<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {--name=Administrator}';

    protected $description = 'Create (or reset) an administrator account. Admins cannot register from the website.';

    public function handle(): int
    {
        $password = $this->secret('Password (min 8 characters)');

        if (strlen((string) $password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name'                => $this->option('name'),
                'password'            => Hash::make($password),
                'role'                => 'admin',
                'status'              => 'active',
                'verification_status' => 'verified',
            ]
        );

        $this->info('Admin account is ready: ' . $this->argument('email'));

        return self::SUCCESS;
    }
}
