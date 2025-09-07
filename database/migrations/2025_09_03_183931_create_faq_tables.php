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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->default('hr');
            $table->timestamps();
        });
        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }
    
    
    public function down(): void
    {
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('faqs');
    }
};
