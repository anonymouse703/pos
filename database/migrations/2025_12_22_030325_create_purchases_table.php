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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained();
            $table->string('invoice_no');
            $table->date('purchase_date'); 
            $table->date('due_date')->nullable(); 
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0); 
            $table->decimal('shipping', 12, 2)->default(0); 
            $table->decimal('total', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0); 
            $table->decimal('balance', 12, 2)->default(0); 
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'credit', 'cheque', 'transfer'])->nullable();
            $table->text('notes')->nullable(); 
            $table->string('reference')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
