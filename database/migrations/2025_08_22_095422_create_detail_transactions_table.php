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
        Schema::create('detail_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('invoice_number');
            $table->string('product_code');
            $table->string('product_name');
            $table->decimal('price_at_time', 10, 2);
            $table->double('disc1', 10, 2);
            $table->double('disc2', 10, 2);
            $table->double('disc3', 10, 2);
            $table->decimal('net_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transactions');
    }
};
