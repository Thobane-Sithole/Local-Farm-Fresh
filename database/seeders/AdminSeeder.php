<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Admin');

        if (! $email || ! $password) {
            $this->command->warn('ADMIN_EMAIL or ADMIN_PASSWORD not set — skipping admin seed.');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command->info("Admin {$email} already exists — skipping.");
            return;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);

        $this->command->info("Admin {$email} created.");
    }
}
