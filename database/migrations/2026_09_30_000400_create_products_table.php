<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 120);
            $table->string('slug', 160)->unique();
            $table->string('short_description', 160)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('unit', 20);
            $table->unsignedInteger('quantity_available')->default(0);
            $table->boolean('is_available')->default(true);

            // Admin moderation is separate from the farmer's own on/off switch,
            // so a farmer can't re-enable a product an admin removed.
            $table->timestamp('removed_at')->nullable();
            $table->string('removal_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Public listing query: available, not removed, filtered by category, newest first.
            $table->index(['is_available', 'category_id', 'created_at']);
            $table->index(['farmer_profile_id', 'is_available']);
            $table->index('price');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
