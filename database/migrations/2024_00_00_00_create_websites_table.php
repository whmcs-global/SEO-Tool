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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('GOOGLE_ANALYTICS_CLIENT_ID');
            $table->string('GOOGLE_ANALYTICS_CLIENT_SECRET');
            $table->string('GOOGLE_ANALYTICS_REDIRECT_URI');
            $table->string('API_KEY');
            $table->string('GOOGLE_ADS_DEVELOPER_TOKEN');
            $table->string('GOOGLE_ADS_CLIENT_ID');
            $table->string('GOOGLE_ADS_CLIENT_SECRET');
            $table->string('GOOGLE_ADS_REDIRECT_URI');
            $table->string('GOOGLE_ADS_KEY');
            $table->string('GOOGLE_ADS_LOGIN_CUSTOMER_ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
