<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;
    protected $fillable = ['dateCommande', 'etat', 'prixTotal', 'status'];

    public function produits()
    {
        return $this->hasMany(ProduitCommande::class);
    }
    // Relation avec Paiement
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

}

