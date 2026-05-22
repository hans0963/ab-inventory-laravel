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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('suppliers_name');
            $table->string('suppliers_company')->nullable();
            $table->string('suppliers_email')->nullable();
            $table->string('suppliers_phone')->nullable();
            $table->text('suppliers_address')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_name');
            $table->string('employee_email')->nullable();
            $table->string('employee_phone')->nullable();
            $table->text('employee_address')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('customer_type', ['Regular', 'Walk-in', 'Senior', 'PWD', 'VIP'])->default('Regular');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('suppliers');
    }
};
