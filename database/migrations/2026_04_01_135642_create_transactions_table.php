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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->decimal('final_price', 15, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'ewallet']);
            $table->enum('transaction_status', ['pending', 'accepted', 'completed', 'cancelled']);
            $table->enum('payment_status', ['pending', 'paid', 'failed']);
            $table->dateTime('scheduled_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
