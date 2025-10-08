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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('plan', 50)->default('default');
            $table->enum('period', ['monthly', 'yearly'])->default('yearly');
            $table->decimal('price', 10, 2)->default(25.00);
            $table->string('currency', 3)->default('EUR');

            $table->enum('status', ['trialing','active','paused','canceled','expired'])->default('active');
            $table->boolean('is_auto_renew')->default(true);

            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->date('next_renewal_on')->nullable();
            $table->date('trial_ends_on')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['next_renewal_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
