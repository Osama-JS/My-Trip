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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 14, 4);
            $table->decimal('balance_before', 14, 4);
            $table->decimal('balance_after', 14, 4);
            $table->text('description')->nullable();
            
            // Polymorphic reference to link transaction to an order, booking, refund, etc.
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
