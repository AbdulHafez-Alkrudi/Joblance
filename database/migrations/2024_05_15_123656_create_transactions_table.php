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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->double('balance');
            $table->string('code')->nullable();

            $table->foreignId('transaction_type_id')->constrained('transactions_types')->cascadeOnDelete();
            $table->foreignId('transaction_status_id')->constrained('transaction_statuses')->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
