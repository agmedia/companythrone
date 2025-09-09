<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->unsignedInteger('clicks')->nullable();
            $table->timestamps();

            $table->index(['status']);
        });

        Schema::create('banner_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slogan')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->unique(['banner_id','locale']);
            // Ako želiš jedinstvene “naslove-slugove” po jeziku, dodaj i slug kolonu ovdje;
            // ako ne treba slug za bannere, preskoči.
            $table->index(['locale']);
        });

        Schema::create('banner_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();

            $table->index(['start_date','end_date','position']);
        });

    }


    public function down(): void
    {
        Schema::dropIfExists('banner_schedules');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('banner_translations');
    }
};
