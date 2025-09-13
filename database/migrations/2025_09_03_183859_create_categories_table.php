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
            $table->string('group')->index(); // products, blog, pages, footer, navigation...
            $table->boolean('is_active')->default(true);
            $table->boolean('is_navbar')->default(false); // If "category" is only a link (eg. if it should link to a page)
            $table->unsignedInteger('clicks')->default(0);
            $table->string('position')->nullable(); //eg. 'top-left' or 'left'. if group footer, is_navbar true, position left.. would mean left column in footer template....
            $table->integer('sort_order')->default(0);
            $table->nestedSet();
            $table->timestamps();

            $table->index(['is_active']);
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('locale', 5)->index(); // hr, en, etc.
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('link_url')->nullable();
            $table->mediumText('description')->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->json('seo_json')->nullable();

            $table->unique(['category_id', 'locale']);
            $table->unique(['locale','slug']); // VAŽNO umjesto unique('slug')
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
