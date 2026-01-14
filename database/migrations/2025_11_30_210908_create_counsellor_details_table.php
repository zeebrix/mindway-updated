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
        Schema::create('counsellor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('gender')->nullable();
            $table->string('description')->nullable();
            $table->string('intake_link')->nullable();
            $table->string('timezone')->nullable();
            $table->text('avatar')->nullable();
            $table->string('specialization')->nullable();
            $table->string('language')->nullable();
            $table->string('location')->nullable();
            $table->string('communication_method')->nullable();
            $table->string('introduction_video')->nullable();
            $table->string('notice_period')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counsellor_details');
    }
};
