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
        Schema::table('payments', function (Blueprint $table) {
            // Veza na pretplatu (nije obavezno, jer može postojati povijesna uplata bez veze)
            $table->foreignId('subscription_id')->nullable()->after('company_id')
                  ->constrained()->nullOnDelete();

            // Valuta/metoda + račun
            $table->string('currency', 3)->default('EUR')->after('amount');
            $table->string('method')->nullable()->after('provider'); // npr. card, bank, paypal
            $table->string('invoice_no', 50)->nullable()->unique()->after('paid_at');

            // Porezi (ako zatreba)
            $table->decimal('vat_rate', 5, 2)->nullable()->after('amount');
            $table->decimal('tax_amount', 10, 2)->nullable()->after('vat_rate');
            $table->decimal('net_amount', 10, 2)->nullable()->after('tax_amount');

            // Slobodan JSON za providera (webhook payload, meta…)
            $table->json('meta')->nullable()->after('provider_ref');

            // Indeksi
            $table->index(['subscription_id']);
            $table->index(['status', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['subscription_id']);
            $table->dropIndex(['status', 'period_start']);
            $table->dropConstrainedForeignId('subscription_id');

            $table->dropColumn(['currency','method','invoice_no','vat_rate','tax_amount','net_amount','meta']);
        });
    }
};
