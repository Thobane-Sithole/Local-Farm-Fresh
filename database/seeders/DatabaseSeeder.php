<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        // Never create demo accounts with known passwords in production.
        // Create the first admin there with: php artisan lff:create-admin
        if (app()->isProduction()) {
            return;
        }

        $this->call(MarketplaceSeeder::class);
    }
}
