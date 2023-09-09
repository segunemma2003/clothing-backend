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
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('code');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('product_id');
            $table->string('product_code');
            $table->decimal('total_price')->decimal(0.00);
            $table->decimal('sub_total')->decimal(0.00);
            $table->decimal('delivery_fee')->decimal(0.00);
            $table->decimal('discount')->decimal(0.00);
            $table->integer('quantity')->default(1);
            $table->decimal('tax_rate')->decimal(0.00);
            $table->string('size')->nullable();
            $table->string('measurements')->nullable();
            $table->boolean('is_measurement')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
