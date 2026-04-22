<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id(); // Only one auto-increment primary key
            $table->unsignedBigInteger('product_id'); // Correct foreign key type
            $table->unsignedBigInteger('employee_id'); // Correct foreign key type
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->date('date');
            $table->integer('balance_forwarded')->default(0);
            $table->integer('pull_out')->default(0);
            $table->integer('new_balance')->default(0);
            $table->integer('new_luto')->default(0);
            $table->integer('total_inventory')->default(0);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
};


