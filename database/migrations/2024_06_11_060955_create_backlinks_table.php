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
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->nullable()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade');
            $table->string('website');
            $table->string('url');
            $table->string('backlink_source');
            $table->string('link_type');
            $table->intiger('spam_score')->default(0);
            $table->string('anchor_text');
            $table->integer('domain_authority')->default(0);
            $table->integer('page_authority')->default(0);
            $table->string('contact_person');
            $table->text('notes_comments')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Declined'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlinks');
    }
};
