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
        // create_companies_and_translations.php
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->nullable()->constrained()->nullOnDelete();
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
            $table->unsignedInteger('clicks')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'is_link_active']);
        });

        Schema::create('company_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5); // hr | en
            $table->string('name');
            $table->string('slug');
            $table->string('slogan')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->unique(['company_id','locale']);   // jedna linija po jeziku
            $table->unique(['locale','slug']);         // slug jedinstven unutar jezika
            $table->index(['locale']);                 // ubrza binding
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_translations');
    }
};
