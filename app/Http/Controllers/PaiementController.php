<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Inclure la relation avec 'typePaiement' et sélectionner les informations nécessaires
        $paiements = Paiement::with('typePaiement:id,description') // Sélectionner seulement l'ID et la description
                    ->get()
                    ->map(function ($paiement) {
                        $paiement->typePaiementDescription = $paiement->typePaiement ? $paiement->typePaiement->description : 'Non renseigné';
                        return $paiement;
                    });
    
        return response()->json($paiements, 200);
    }
    
    public function handleCallback(Request $request)
{
    $token = $request->header('PAYDUNYA-TOKEN');
    $expectedToken = config('paydunya.token');

    if ($token !== $expectedToken) {
        return response()->json(['message' => 'Token non valide'], 403);
    }

    // Processus du paiement
    $data = $request->all();
    $commande = Commande::findOrFail($data['custom_data']['commande_id']);

    if ($data['status'] === 'completed') {
        // Marquer la commande comme payée
        $commande->status = 'Payé';
        $commande->save();

        // Mettre à jour le paiement
        $paiement = Paiement::where('commande_id', $commande->id)->first();
        if ($paiement) {
            $paiement->status = 'Réussi';
            $paiement->transaction_id = $data['transaction_id'];
            $paiement->save();
        }

        return response()->json(['message' => 'Paiement confirmé avec succès.'], 200);
    }

    return response()->json(['message' => 'Échec du paiement.'], 400);
}

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données entrantes
        $request->validate([
            'datePaiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'typePaiementId' => 'required|exists:type_paiements,id',
            'commande_id' => 'required|exists:commandes,id' // Assure que commande_id est valide
        ]);
    
        // Crée un nouveau paiement
        $paiement = Paiement::create([
            'datePaiement' => $request->datePaiement,
            'montant' => $request->montant,
            'typePaiementId' => $request->typePaiementId,
            'commande_id' => $request->commande_id, // Associe la commande au paiement
        ]);
    
        return response()->json($paiement, Response::HTTP_CREATED);
    }


    
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère un paiement spécifique
        $paiement = Paiement::find($id);

        if ($paiement) {
            return response()->json($paiement, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'datePaiement' => 'sometimes|required|date',
            'montant' => 'sometimes|required|numeric|min:0',
            'typePaiementId' => 'sometimes|required|exists:type_paiements,id',
        ]);

        // Récupère et met à jour le paiement
        $paiement = Paiement::find($id);

        if ($paiement) {
            $paiement->update($request->all());
            return response()->json($paiement, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime un paiement
        $paiement = Paiement::find($id);

        if ($paiement) {
            $paiement->delete();
            return response()->json(['message' => 'Paiement supprimé avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Paiement non trouvé'], Response::HTTP_NOT_FOUND);
    }
}
