<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('fname');
            $table->string('lname')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();

            // jednostavni path do datoteke (public/storage/avatars/...), default placeholder
            $table->string('avatar')->default('media/avatars/default_avatar.png');

            $table->longText('bio')->nullable();
            $table->string('social')->nullable(); // može URL ili mali JSON string

            // radi integriteta koristimo enum (može i string ako želiš)
            $table->enum('role', ['master', 'admin', 'manager', 'editor', 'customer'])->default('customer');

            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['role', 'status']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
