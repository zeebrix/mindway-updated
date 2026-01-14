<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('course_lessons')->cascadeOnDelete();

            $table->string('audio_url')->nullable();
            $table->string('video_url')->nullable();
            $table->longText('article_text')->nullable();

            $table->string('thumbnail')->nullable();
            $table->string('host_image')->nullable();
            $table->string('host_name')->nullable();
            $table->string('author_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_media');
    }
}
