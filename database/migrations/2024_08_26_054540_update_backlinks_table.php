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
        Schema::table('backlinks', function (Blueprint $table) {
            $table->enum('aproval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('notes_comments');
            $table->unsignedBigInteger('approved_by')->nullable()->after('aproval_status');
            $table->string('reason')->nullable()->after('approved_by');
            $table->string('email')->nullable()->after('reason');
            $table->string('password')->nullable()->after('email');
            $table->string('login_url')->nullable()->after('password');
            $table->string('company_name')->nullable()->after('login_url');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
