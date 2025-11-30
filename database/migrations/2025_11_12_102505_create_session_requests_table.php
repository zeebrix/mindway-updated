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
        Schema::create('session_requests', function (Blueprint $table) {
            $table->id();

            // The customer who is requesting the session
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('program_id')
                ->constrained('users')
                ->onDelete('cascade');
            // The program for which the session is requested
            $table->foreignId('program_detail_id')
                ->constrained('program_details')
                ->onDelete('cascade');

            // The counselor assigned to this session
            $table->foreignId('counselor_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->integer('requested_days'); // Better naming
            $table->text('request_reason');    // More descriptive
            $table->enum('status', ['pending', 'accepted', 'denied'])->default('pending'); // Enum for status

            $table->date('request_date')->nullable();
            $table->date('denied_date')->nullable();
            $table->date('accepted_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_requests');
    }
};
