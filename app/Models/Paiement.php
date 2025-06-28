<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $fillable = ['datePaiement', 'montant', 'typePaiementId','commande_id'];
    // Relation avec Commande
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    // Relation avec TypePaiement
    public function typePaiement()
    {
        return $this->belongsTo(TypePaiement::class, 'typePaiementId');
    }
}
