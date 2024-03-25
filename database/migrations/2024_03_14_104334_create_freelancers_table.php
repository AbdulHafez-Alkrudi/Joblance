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
        Schema::create('freelancers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')      ->constrained();
            $table->foreignId('study_case_id')->constrained();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');

            $table->string('location');
            $table->string('major');
            $table->boolean('open_to_work');
          
            $table->string('image')->nullable();
            $table->text('bio');
            $table->timestamps();
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancers');
    }
};
