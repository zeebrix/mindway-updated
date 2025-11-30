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
        Schema::create('counselling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('counselor_id')->constrained('users');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('program_id')->nullable()->constrained('program_details');
            $table->date('session_date');
            $table->string('session_type');
            $table->string('reason')->nullable();
            $table->boolean('is_new_user')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counselling_sessions');
    }
};
