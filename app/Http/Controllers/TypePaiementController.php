<?php

namespace App\Http\Controllers;

use App\Models\TypePaiement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypePaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère tous les types de paiements
        $typePaiements = TypePaiement::all();
        return response()->json($typePaiements, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données entrantes
        $request->validate([
            'description' => 'required|string|max:255|unique:type_paiements,description',
        ]);

        // Crée un nouveau type de paiement
        $typePaiement = TypePaiement::create([
            'description' => $request->description,
        ]);

        return response()->json($typePaiement, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère un type de paiement spécifique
        $typePaiement = TypePaiement::find($id);

        if ($typePaiement) {
            return response()->json($typePaiement, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Type de paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'description' => 'required|string|max:255|unique:type_paiements,description,' . $id,
        ]);

        // Récupère et met à jour le type de paiement
        $typePaiement = TypePaiement::find($id);

        if ($typePaiement) {
            $typePaiement->update($request->all());
            return response()->json($typePaiement, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Type de paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime un type de paiement
        $typePaiement = TypePaiement::find($id);

        if ($typePaiement) {
            $typePaiement->delete();
            return response()->json(['message' => 'Type de paiement supprimé avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Type de paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }
}
