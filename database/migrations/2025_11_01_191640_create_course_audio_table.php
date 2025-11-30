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
        Schema::create('course_audio', function (Blueprint $table) {
            $table->id();
            $table->string('audio')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('audio_title')->nullable();
            $table->string('duration')->nullable();
            $table->string('total_play')->nullable();
            $table->string('course_order_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_audio');
    }
};
