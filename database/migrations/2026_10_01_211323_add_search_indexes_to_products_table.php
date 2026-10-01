<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only runs on PostgreSQL; SQLite ignores these gracefully.
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX IF NOT EXISTS products_name_trgm ON products USING GIN (name gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_description_trgm ON products USING GIN (description gin_trgm_ops)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS products_name_trgm');
        DB::statement('DROP INDEX IF EXISTS products_description_trgm');
    }
};
