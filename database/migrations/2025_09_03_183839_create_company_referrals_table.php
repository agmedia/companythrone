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
        Schema::create('company_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('referred_company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['referrer_company_id','referred_company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_referrals');
    }
};
