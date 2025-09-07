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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('oib', 20)->unique();
            $table->string('street')->nullable();
            $table->string('street_no')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_link_active')->default(false);
            $table->unsignedInteger('referrals_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
