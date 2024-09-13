<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'search_volume',
                'clicks',
                'impression',
                'competition',
                'bid_rate_low',
                'bid_rate_high'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
}
