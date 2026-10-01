<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('farm_name', 120);
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();

            // Location: entered manually in v1. Lat/lng reserved for future
            // "farmers near me" features; nothing requires GPS today.
            $table->string('country', 2)->default('ZA');
            $table->string('province', 30)->index();
            $table->string('municipality', 120);
            $table->string('town', 120)->nullable();
            $table->string('farm_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Cloudinary asset; public_id is kept so we can delete/replace it.
            $table->string('profile_image_url')->nullable();
            $table->string('profile_image_public_id')->nullable();

            // Farmers set their own flat delivery fee (v1 keeps delivery simple).
            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['province', 'municipality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_profiles');
    }
};
