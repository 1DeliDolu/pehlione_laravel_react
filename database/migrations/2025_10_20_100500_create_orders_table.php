<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->unsignedBigInteger('discount_code_id')->nullable();
            $table->string('status')->default('processing');
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('shipping_total', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->json('shipping_address');
            $table->json('billing_address');
            $table->string('shipping_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('cart_id');
            $table->index('discount_code_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
