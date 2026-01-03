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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('cost_price', 12, 2);
            $table->decimal('unit_cost', 12, 2); // Cost per unit before tax
            $table->decimal('discount', 12, 2)->default(0); // Line item discount
            $table->decimal('tax_rate', 8, 2)->default(0); // Tax rate for this item
            $table->decimal('tax_amount', 12, 2)->default(0); // Calculated tax for this line
            $table->decimal('total', 12, 2); // Total cost for this line item
            $table->string('batch_number')->nullable(); // For batches/lots
            $table->date('expiry_date')->nullable(); // For perishable materials
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
