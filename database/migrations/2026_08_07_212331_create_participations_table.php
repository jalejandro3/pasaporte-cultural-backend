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
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->uuid('assistant_id');
            $table->unsignedBigInteger('activity_id');
            $table->enum('status', ['IN_PROCESS', 'COMPLETED', 'NOT_COMPLETED']);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->timestamps();
            $table->unique(['activity_id', 'assistant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
