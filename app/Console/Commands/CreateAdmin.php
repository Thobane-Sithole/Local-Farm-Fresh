<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Production-safe way to create the first admin (seeders don't create users in production).
 *   php artisan lff:create-admin
 */
class CreateAdmin extends Command
{
    protected $signature = 'lff:create-admin';

    protected $description = 'Create a Local-Farm-Fresh administrator account';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Full name'),
            'email' => $this->ask('Email address'),
            'password' => $this->secret('Password'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User($data);
        $user->role = UserRole::Admin;
        $user->email_verified_at = now();
        $user->save();

        $this->info("Admin {$user->email} created.");

        return self::SUCCESS;
    }
}
