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
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
    
};
