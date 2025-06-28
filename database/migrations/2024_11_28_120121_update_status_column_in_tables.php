<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusColumnInTables extends Migration
{
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['disponible', 'occupée', 'reservée'])->default('disponible')->change();
        });
    }

    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('status')->default('Disponible')->change();
        });
    }
}
