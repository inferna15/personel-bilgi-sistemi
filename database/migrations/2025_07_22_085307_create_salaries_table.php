<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->date('pay_date');
            $table->decimal('net_salary');
            $table->decimal('gross_salary');
            $table->string('payroll_file_path');
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'pay_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
