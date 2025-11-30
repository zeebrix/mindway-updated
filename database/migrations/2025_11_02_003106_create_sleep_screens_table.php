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
        Schema::create('sleep_screens', function (Blueprint $table) {
            $table->id();
            $table->string('audio_title');
            $table->string('sleep_audio');
            $table->string('image')->nullable();
            $table->string('duration');
            $table->unsignedInteger('total_play')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sleep_screens');
    }
};
