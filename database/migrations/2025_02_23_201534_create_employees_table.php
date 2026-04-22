<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('employee_name', 100);
            $table->string('employee_email', 50)->unique();
            $table->string('employee_phone')->nullable();
            $table->string('position', 50);
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('employees');
    }
};

