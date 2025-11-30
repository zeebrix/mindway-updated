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
        Schema::create('program_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('program_detail_id')
                ->constrained()
                ->onDelete('cascade');


            $table->string('type')->nullable();
            $table->integer('annual_fee')->nullable();
            $table->integer('session_cost')->nullable();
            $table->date('renewal_date')->nullable();
            $table->boolean('gst_registered')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_plans');
    }
};
