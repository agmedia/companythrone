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
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->nestedSet(); // kalnoy/nestedset adds _lft, _rgt, parent_id
            $table->timestamps();
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
    }
};
