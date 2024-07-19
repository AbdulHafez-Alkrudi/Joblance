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
        Schema::create('job_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('job_type_id')->constrained('job_types')->onDelete('cascade');
            $table->foreignId('experience_level_id')->constrained('experience_levels')->onDelete('cascade');
            $table->foreignId('remote_id')->constrained('remotes')->onDelete('cascade');
            $table->integer('salary');
            $table->string('location')->nullable();
            $table->text('job_description');
            $table->text('requirements');
            $table->text('additional_information')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('show_number_of_employees')->default(false);
            $table->boolean('show_about_the_company')->default(false);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_details');
    }
};
