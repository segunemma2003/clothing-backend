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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean("send_mail")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean("send_mail")->default(false);
        });
    }
};
