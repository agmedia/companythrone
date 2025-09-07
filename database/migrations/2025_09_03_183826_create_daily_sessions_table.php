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
        Schema::create('daily_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('day');
            $table->boolean('completed_25')->default(false);
            $table->unsignedTinyInteger('completed_count')->default(0);
            $table->json('slots_payload');
            $table->timestamp('last_action_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id','day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sessions');
    }
};
