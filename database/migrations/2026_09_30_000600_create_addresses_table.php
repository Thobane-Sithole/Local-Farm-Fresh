<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 40)->default('Home');
            $table->string('recipient_name', 120);
            $table->string('phone', 20);
            $table->string('street_address');
            $table->string('suburb', 120)->nullable();
            $table->string('town', 120);
            $table->string('municipality', 120)->nullable();
            $table->string('province', 30);
            $table->string('postal_code', 10)->nullable();
            $table->string('delivery_notes', 500)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
