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
        Schema::disableForeignKeyConstraints();
        Schema::create('pay_pal_orders', function (Blueprint $table) {
            $table->id('paypal_order_id');
            $table->string('order_id');
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('amount');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_pal_orders');
    }
};
