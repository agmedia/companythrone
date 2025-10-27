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
        Schema::table('user_details', function ($table) {
            $table->string('referral_code')->nullable()->after('phone');
            $table->tinyInteger('referral_code_used')->nullable()->after('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function ($table) {
            $table->dropColumn('referral_code');
            $table->dropColumn('referral_code_used');
        });
    }
};
