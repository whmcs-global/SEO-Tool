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
        Schema::create('keyword_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->integer('position')->nullable();
            $table->integer('search_volume')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('impression')->nullable();
            $table->decimal('competition', 8, 2)->nullable();
            $table->decimal('bid_rate_low', 8, 2)->nullable();
            $table->decimal('bid_rate_high', 8, 2)->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_data');
    }
};
