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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_code');
            $table->string('product_name');
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('subtotal_profit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
