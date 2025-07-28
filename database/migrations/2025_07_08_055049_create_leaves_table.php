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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete('cascade');
            $types = array_column(\App\Enum\LeaveType::cases(), 'value');
            $table->enum('type', $types);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->string('reason');
            $status = array_column(\App\Enum\LeaveStatus::cases(), 'value');
            $table->enum('status', $status)->default(\App\Enum\LeaveStatus::PENDING->value);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->cascadeOnDelete('set null');
            $table->date('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
