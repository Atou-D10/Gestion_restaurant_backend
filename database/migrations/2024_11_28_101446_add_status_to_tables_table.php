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
    Schema::table('tables', function (Blueprint $table) {
        $table->enum('status', ['disponible', 'reservée'])->default('disponible');
    });
}

public function down()
{
    Schema::table('tables', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
