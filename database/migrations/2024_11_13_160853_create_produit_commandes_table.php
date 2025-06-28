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
    Schema::create('produit_commandes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('commande_id')->constrained();
        $table->foreignId('produit_id')->constrained();
        $table->integer('quantite');
        $table->double('prix', 8, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produit_commandes');
    }
};
