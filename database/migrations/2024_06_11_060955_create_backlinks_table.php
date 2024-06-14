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
            $table->foreignId('website_id')->constrained()->onDelete('cascade')->nullable();
            $table->date('date');
            $table->string('website');
            $table->string('url');
            $table->string('target_keyword');
            $table->string('backlink_source');
            $table->enum('link_type', ['Guest Post', 'Infographics', 'Sponsored Content']);
            $table->string('anchor_text');
            $table->string('domain_authority');
            $table->string('page_authority');
            $table->string('contact_person');
            $table->text('notes_comments')->nullable();
            $table->boolean('status')->default(0);
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
