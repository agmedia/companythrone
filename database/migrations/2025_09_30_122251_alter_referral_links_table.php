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
        Schema::table('referral_links', function ($table) {
            $table->string('title')->nullable()->after('label');
            $table->string('phone')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('referral_links', function ($table) {
            $table->dropColumn('title');
            $table->dropColumn('phone');
        });
    }
};
