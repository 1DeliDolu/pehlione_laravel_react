<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('size_profile')->default('standard_apparel');
            $table->json('available_sizes')->nullable();
            $table->string('material_profile')->nullable();
            $table->json('attribute_tags')->nullable();
            $table->json('sustainability_notes')->nullable();
            $table->json('care_instructions')->nullable();
            $table->json('images')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->string('stock_status')->default('in_stock');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('lead_time_days')->nullable();
            $table->string('energy_label')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
