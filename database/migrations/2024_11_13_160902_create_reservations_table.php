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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('dateReservation');
            $table->time('heureDebut');
            $table->integer('nbrPersonne');
            $table->time('heureFin');
            $table->integer('numeroTable');
            $table->unsignedBigInteger('client_id'); // Ici, seulement le champ sans la contrainte
            $table->timestamps();
        });
    }
    
    
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
