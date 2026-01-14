<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLessonsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->string('audio')->nullable();
            $table->string('video')->nullable();
            $table->text('article_text')->nullable();
            $table->string('host_name')->nullable();
            $table->string('author_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('audio');
            $table->dropColumn('video');
            $table->dropColumn('article_text');
            $table->dropColumn('host_name');
            $table->dropColumn('author_name');
        });
    }
}
