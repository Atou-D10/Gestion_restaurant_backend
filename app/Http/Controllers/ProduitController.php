<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère tous les produits
        $produits = Produit::all();
        return response()->json($produits, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'libelle' => 'required|string|max:255',
                'description' => 'nullable|string',
                'prix' => 'required|numeric',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Limite à 2 Mo
            ]);
    
            // Gestion de l'image (si présente)
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('produits', 'public'); // Sauvegarde dans storage/app/public/produits
                $validatedData['image'] = $path; // Enregistre le chemin dans les données validées
            }
    
            // Création du produit
            $produit = Produit::create($validatedData);
    
            return response()->json($produit, 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(), // Renvoie les erreurs de validation spécifiques
            ], 422);
        } catch (\Exception $e) {
            // Logger l'erreur pour le débogage
            \Log::error('Erreur lors de la création du produit : ' . $e->getMessage());
    
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère un produit spécifique
        $produit = Produit::find($id);
        
        if ($produit) {
            return response()->json($produit, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'libelle' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
        ]);

        // Récupère et met à jour le produit
        $produit = Produit::find($id);
        
        if ($produit) {
            $produit->update($request->all());
            return response()->json($produit, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime un produit
        $produit = Produit::find($id);
        
        if ($produit) {
            $produit->delete();
            return response()->json(['message' => 'Produit supprimé avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
    }
}
