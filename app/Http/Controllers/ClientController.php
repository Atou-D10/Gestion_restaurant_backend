<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client; // Assurez-vous que le modèle Client existe.

class ClientController extends Controller
{
    /**
     * Afficher la liste de tous les clients.
     */
    public function index()
    {
        try {
            $clients = Client::all(); // Récupérer tous les clients
            return response()->json($clients, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des clients.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Enregistrer un nouveau client.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:clients',
                'matricule' => 'nullable|string|unique:clients',
            ]);

            $client = Client::create($validated);
            return response()->json(['message' => 'Client ajouté avec succès.', 'client' => $client], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'ajout du client.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Afficher un client spécifique.
     */
    public function show()
    {
        try {
            // Récupérer l'ID du client authentifié
            $clientId = auth()->id();

            // Si aucun utilisateur n'est authentifié
            if (!$clientId) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Charger les informations du client en utilisant l'ID
            $client = Client::findOrFail($clientId);

            // Retourner les données du client
            return response()->json($client);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération du client: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mettre à jour un client spécifique.
     */
    public function update(Request $request, $id)
    {
        // Validation des données du client
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $id,
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:15',
        ]);
    
        // Trouver le client par ID
        $client = Client::findOrFail($id);
    
        // Mettre à jour les informations
        $client->update($validated);
    
        // Retourner la réponse avec les données du client mises à jour
        return response()->json($client);
    }
    

    /**
     * Supprimer un client.
     */
    public function destroy(string $id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();

            return response()->json(['message' => 'Client supprimé avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du client.', 'error' => $e->getMessage()], 500);
        }
    }
}
