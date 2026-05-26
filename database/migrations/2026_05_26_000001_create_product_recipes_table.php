<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('product_recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_unit', 12, 3);
            $table->timestamps();

            $table->unique(['product_recipe_id', 'raw_material_id'], 'recipe_material_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipe_ingredients');
        Schema::dropIfExists('product_recipes');
    }
};
