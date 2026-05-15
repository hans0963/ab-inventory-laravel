<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('quantity'); // Positive for Stock In, Negative for Stock Out
            $table->enum('type', ['IN', 'OUT']);
            $table->string('reason')->nullable(); // e.g., Purchase, Usage, Spoilage, Correction
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_movements');
    }
};
