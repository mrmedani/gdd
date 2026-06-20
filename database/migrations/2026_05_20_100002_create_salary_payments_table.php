<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('salary_payments');
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('advances_deducted', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('payment_method')->default('cash');
            $table->string('transaction_reference')->nullable();
            $table->date('paid_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
