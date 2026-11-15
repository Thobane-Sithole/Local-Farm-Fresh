<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique index first — required by SQLite before dropping the column
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });
    }
};
