<?php

namespace App\Http\Controllers;

use App\Models\ProduitCommande;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProduitCommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère toutes les relations produit-commande
        $produitCommandes = ProduitCommande::all();
        return response()->json($produitCommandes, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données entrantes
        $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
        ]);

        // Crée une nouvelle relation produit-commande
        $produitCommande = ProduitCommande::create([
            'commande_id' => $request->commande_id,
            'produit_id' => $request->produit_id,
            'quantite' => $request->quantite,
            'prix' => $request->prix,
        ]);

        return response()->json($produitCommande, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère une relation produit-commande spécifique
        $produitCommande = ProduitCommande::find($id);

        if ($produitCommande) {
            return response()->json($produitCommande, Response::HTTP_OK);
        }

        return response()->json(['message' => 'ProduitCommande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'commande_id' => 'sometimes|required|exists:commandes,id',
            'produit_id' => 'sometimes|required|exists:produits,id',
            'quantite' => 'sometimes|required|integer|min:1',
            'prix' => 'sometimes|required|numeric|min:0',
        ]);

        // Récupère et met à jour la relation produit-commande
        $produitCommande = ProduitCommande::find($id);

        if ($produitCommande) {
            $produitCommande->update($request->all());
            return response()->json($produitCommande, Response::HTTP_OK);
        }

        return response()->json(['message' => 'ProduitCommande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime une relation produit-commande
        $produitCommande = ProduitCommande::find($id);

        if ($produitCommande) {
            $produitCommande->delete();
            return response()->json(['message' => 'ProduitCommande supprimée avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'ProduitCommande non trouvée'], Response::HTTP_NOT_FOUND);
    }
}
