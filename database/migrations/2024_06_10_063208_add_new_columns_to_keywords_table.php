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
        Schema::table('keywords', function (Blueprint $table) {
            $table->integer('position')->nullable();
            $table->integer('search_volume')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('impression')->nullable();
            $table->decimal('competition', 8, 2)->nullable();
            $table->decimal('bid_rate_low', 8, 2)->nullable();
            $table->decimal('bid_rate_high', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('search_volume');
            $table->dropColumn('clicks');
            $table->dropColumn('impression');
            $table->dropColumn('competition');
            $table->dropColumn('bid_rate_low');
            $table->dropColumn('bid_rate_high');
        });
    }
};
