<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daily counter behind human-friendly numbers like LFF-20260930-0001.
        Schema::create('order_number_sequences', function (Blueprint $table) {
            $table->date('sequence_date')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        // One order per farmer. A cart with products from three farmers becomes
        // three orders sharing a checkout_group, because each farmer confirms,
        // prepares and delivers their own goods and collects their own cash.
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->uuid('checkout_group')->index();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('farmer_profile_id')->constrained()->restrictOnDelete();

            // Delivery details are copied onto the order so history stays accurate
            // even if the customer later edits or deletes the saved address.
            $table->string('delivery_name', 120);
            $table->string('delivery_phone', 20);
            $table->string('delivery_street');
            $table->string('delivery_suburb', 120)->nullable();
            $table->string('delivery_town', 120);
            $table->string('delivery_municipality', 120)->nullable();
            $table->string('delivery_province', 30);
            $table->string('delivery_postal_code', 10)->nullable();
            $table->string('delivery_notes', 500)->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method', 30)->default('cash_on_delivery');
            $table->string('status', 30)->default('pending');

            $table->timestamp('placed_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['farmer_profile_id', 'status', 'placed_at']);
            $table->index(['customer_id', 'placed_at']);
            $table->index(['status', 'placed_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Nullable so a deleted product doesn't erase order history.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 120);
            $table->string('unit', 20);
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        // Powers the customer's tracking timeline ("Confirmed 10:24", "Preparing 10:32" ...).
        Schema::create('order_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_events');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_number_sequences');
    }
};
