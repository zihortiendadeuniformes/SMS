<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin {email} {password} {name=Admin}';
    protected $description = 'Create or update an admin user';

    public function handle(): int
    {
        $user = User::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name'              => $this->argument('name'),
                'password'          => Hash::make($this->argument('password')),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        $this->info("User [{$user->email}] ready with role super-admin.");
        return 0;
    }
}
