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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->integer('stock_alert_threshold')->default(10);
            $table->enum('inventory_type', ['Finished Product', 'Raw Material'])->default('Finished Product');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_name');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type')->nullable(); // e.g., 'Packaging', 'Ingredient'
            $table->integer('quantity')->default(0);
            $table->string('unit')->nullable(); // e.g., 'kg', 'L', 'pcs'
            $table->date('expiration_date')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->integer('stock_alert_threshold')->default(10);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
