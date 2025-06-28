<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use App\Models\TypePaiement;
use App\Models\ProduitCommande; // Assurez-vous que ce modèle est importé
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
    public function getRapport()
{
    $dateDuJour = now()->format('Y-m-d');

    // Récupérer les commandes par état et statut
    $commandesParEtat = Commande::select('etat', DB::raw('COUNT(*) as count'))
        ->whereDate('created_at', $dateDuJour)
        ->groupBy('etat')
        ->get();

    $commandesParStatut = Commande::select('status', DB::raw('COUNT(*) as count'))
        ->whereDate('created_at', $dateDuJour)
        ->groupBy('status')
        ->get();

    // Récupérer la recette totale
    $recetteTotale = Paiement::whereDate('created_at', $dateDuJour)->sum('montant');
    $nombrePaiements = Paiement::whereDate('created_at', $dateDuJour)->count();
    $moyennePaiement = $nombrePaiements > 0 ? $recetteTotale / $nombrePaiements : 0;

    // Recette par mode de paiement
    $parModePaiement = Paiement::whereDate('created_at', $dateDuJour)
        ->select('typePaiementId', DB::raw('SUM(montant) as total'), DB::raw('COUNT(*) as count'))
        ->groupBy('typePaiementId')
        ->get()
        ->map(function ($paiement) {
            $type = TypePaiement::find($paiement->typePaiementId)->description; // Assurez-vous que cette relation existe
            return [
                'type' => $type,
                'total' => $paiement->total,
                'count' => $paiement->count
            ];
        });
          // Récupérer les produits commandés avec leurs quantités
          $produitsCommandes = ProduitCommande::join('produits', 'produit_commandes.produit_id', '=', 'produits.id')
          ->select('produits.libelle', DB::raw('SUM(produit_commandes.quantite) as total_quantity'))
          ->whereDate('produit_commandes.created_at', $dateDuJour)
          ->groupBy('produits.libelle')
          ->get()
          ->map(function ($produit) {
              return [
                  'libelle' => $produit->libelle,
                  'total_quantity' => $produit->total_quantity
              ];
          });

    return response()->json([
        'date' => $dateDuJour,
        'recette' => [
            'total' => $recetteTotale,
            'nombrePaiements' => $nombrePaiements,
            'moyennePaiement' => $moyennePaiement,
            'parModePaiement' => $parModePaiement
        ],
        'commandesParEtat' => $commandesParEtat,
        'commandesParStatut' => $commandesParStatut,
        'produitsCommandes' => $produitsCommandes // Ajout des produits commandés
     
    ]);
}

}
