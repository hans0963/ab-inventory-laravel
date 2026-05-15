<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_ins', function (Blueprint $table) {
            $table->id();
            $table->string('production_in_no', 50)->unique();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->decimal('total_inventory_value', 12, 2)->default(0); // Based on selling price
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            
            // Audit trail
            $table->unsignedBigInteger('created_by')->nullable();
            $table->date('created_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_ins');
    }
};
