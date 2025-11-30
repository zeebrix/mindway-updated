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
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained('program_details');
            $table->string('company_name')->nullable();
            $table->foreignId('goal_id')->nullable();
            $table->string('gender_preference')->nullable();
            $table->string('meditation_experience')->nullable();
            $table->integer('max_sessions')->nullable();
            $table->string('timezone')->nullable();
            $table->string('preferred_email')->nullable();
            $table->foreignId('department_id')->nullable();
            $table->string('customer_type')->nullable();
            $table->boolean('counselling_user')->default(false);
            $table->boolean('application_user')->default(false);
            $table->boolean('sync_to_brevo')->default(false);
            $table->string('level')->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_details');
    }
};
