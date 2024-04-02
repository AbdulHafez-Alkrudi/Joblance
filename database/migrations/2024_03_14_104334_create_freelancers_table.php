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
            $table->foreignId('major_id')->constrained();
            $table->foreignId('study_case_id')->constrained()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('location')->nullable();
            $table->boolean('open_to_work')->default(false);
            $table->string('image')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
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
