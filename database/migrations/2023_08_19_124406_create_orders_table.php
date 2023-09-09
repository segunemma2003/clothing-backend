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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code')->unique();
            $table->decimal('total_price')->decimal(0.00);
            $table->decimal('sub_total')->decimal(0.00);
            $table->decimal('delivery_fee')->decimal(0.00);
            $table->decimal('tax_rate')->decimal(0.00);
            $table->string('type_of_delivery')->default('pay_on_delivery');
            $table->string('status')->default('pending');
            $table->text('details_of_delivery')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
