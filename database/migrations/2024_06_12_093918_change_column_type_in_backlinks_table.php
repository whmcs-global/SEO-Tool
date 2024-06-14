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
         // Ensure invalid data is cleaned before altering the column type
         $backlinks = DB::table('backlinks')->get();
         foreach ($backlinks as $backlink) {
             if (!is_numeric($backlink->domain_authority)) {
                 DB::table('backlinks')->where('id', $backlink->id)->update(['domain_authority' => 0]);
             }
             if (!is_numeric($backlink->page_authority)) {
                 DB::table('backlinks')->where('id', $backlink->id)->update(['page_authority' => 0]);
             }
             if (!in_array($backlink->status, [0,1])) {
                DB::table('backlinks')->where('id', $backlink->id)->update(['status' => 'Pending']);
            }
         }
        

        Schema::table('backlinks', function (Blueprint $table) {
            $table->integer('domain_authority')->default(0)->change();
            $table->integer('page_authority')->default(0)->change();
            $table->enum('status',['Active', 'Inactive', 'Pending', 'Declined'])->default("Active")->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->string('domain_authority')->change();
            $table->string('page_authority')->change();
            $table->boolean('status')->default(0)->change();
        });
    }
};
