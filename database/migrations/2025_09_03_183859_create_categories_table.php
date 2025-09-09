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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('clicks')->nullable();
            $table->nestedSet();
            $table->timestamps();

            $table->index(['is_active']);
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->unique(['category_id','locale']);
            $table->unique(['locale','slug']); // VAŽNO umjesto unique('slug')
            $table->index(['locale']);
        });

        Schema::create('category_company', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->unique(['category_id', 'company_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('category_company');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_translations');
    }
};
