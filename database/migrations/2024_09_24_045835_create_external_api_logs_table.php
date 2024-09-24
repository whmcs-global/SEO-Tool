<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('external_api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cron_status_id');
            $table->string('api_name');
            $table->text('description')->nullable();
            $table->text('endpoint')->nullable();
            $table->string('method')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('status_code')->nullable();
            $table->timestamps();
            $table->foreign('cron_status_id')->references('id')->on('cron_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_api_logs');
    }
};
