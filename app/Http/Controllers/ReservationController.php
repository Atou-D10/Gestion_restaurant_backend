<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Reservation;
use App\Models\TableDisponibilite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère toutes les réservations
        $reservations = Reservation::all();
        return response()->json($reservations, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'dateReservation' => 'required|date',
            'heureDebut' => 'required|date_format:H:i',
            'heureFin' => 'required|date_format:H:i|after:heureDebut',
            'nbrPersonne' => 'required|integer|min:1',
            'numeroTable' => 'required|exists:tables,numeroTable',
        ]);

        // Vérifier la disponibilité
        $table = Table::where('numeroTable', $request->numeroTable)->first();

        $disponibiliteExistante = TableDisponibilite::where('table_id', $table->id)
            ->where('date', $request->dateReservation)
            ->where(function ($query) use ($request) {
                $query->whereBetween('heureDebut', [$request->heureDebut, $request->heureFin])
                      ->orWhereBetween('heureFin', [$request->heureDebut, $request->heureFin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('heureDebut', '<=', $request->heureDebut)
                            ->where('heureFin', '>=', $request->heureFin);
                      });
            })
            ->exists();

        if ($disponibiliteExistante) {
            return response()->json(['error' => 'Cette table est déjà réservée pour cet horaire.'], 400);
        }

        // Ajouter l'id de l'utilisateur connecté
        $validated['client_id'] = auth()->id();

        // Créer la réservation
        $reservation = Reservation::create($validated);

        // Ajouter la disponibilité dans TableDisponibilite
        TableDisponibilite::create([
            'table_id' => $table->id,
            'reservation_id' => $reservation->id,
            'date' => $request->dateReservation,
            'heureDebut' => $request->heureDebut,
            'heureFin' => $request->heureFin,
        ]);

        return response()->json($reservation, 201);
    }

    /**
     * Vérifie si une table est disponible pour une plage horaire donnée.
     */
    public function verifierDisponibilite(Request $request)
    {
        $validated = $request->validate([
            'dateReservation' => 'required|date',
            'heureDebut' => 'required|date_format:H:i',
            'heureFin' => 'required|date_format:H:i|after:heureDebut',
            'numeroTable' => 'required|exists:tables,numeroTable',
        ]);

        $table = Table::where('numeroTable', $request->numeroTable)->first();

        $disponibiliteExistante = TableDisponibilite::where('table_id', $table->id)
            ->where('date', $request->dateReservation)
            ->where(function ($query) use ($request) {
                $query->whereBetween('heureDebut', [$request->heureDebut, $request->heureFin])
                      ->orWhereBetween('heureFin', [$request->heureDebut, $request->heureFin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('heureDebut', '<=', $request->heureDebut)
                            ->where('heureFin', '>=', $request->heureFin);
                      });
            })
            ->exists();

        if ($disponibiliteExistante) {
            return response()->json(['message' => 'Table non disponible.'], 400);
        }

        return response()->json(['message' => 'Table disponible.'], 200);
    }

    public function getReservationsByClient(Request $request)
    {
    // Récupérer les réservations de l'utilisateur connecté
    $clientId = auth()->id();
    $reservations = Reservation::where('client_id', $clientId)->get();

    return response()->json($reservations);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::find($id);

        if ($reservation) {
            return response()->json($reservation, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'dateReservation' => 'sometimes|required|date',
            'heureDebut' => 'sometimes|required|date_format:H:i',
            'heureFin' => 'sometimes|required|date_format:H:i|after:heureDebut',
            'nbrPersonne' => 'sometimes|required|integer|min:1',
            'numeroTable' => 'sometimes|required|integer',
        ]);

        $reservation = Reservation::find($id);

        if ($reservation) {
            $reservation->update($request->all());

            // Mettre à jour la disponibilité associée
            $disponibilite = TableDisponibilite::where('reservation_id', $reservation->id)->first();
            if ($disponibilite) {
                $disponibilite->update([
                    'date' => $request->dateReservation,
                    'heureDebut' => $request->heureDebut,
                    'heureFin' => $request->heureFin,
                ]);
            }

            return response()->json($reservation, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::find($id);

        if ($reservation) {
            // Supprimer la disponibilité associée
            TableDisponibilite::where('reservation_id', $reservation->id)->delete();

            $reservation->delete();
            return response()->json(['message' => 'Réservation supprimée avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
    }


    /**
     * Récupère les tables disponibles.
     */
    public function getTablesDisponibles()
    {
        $tablesDisponibles = Table::whereDoesntHave('disponibilites', function ($query) {
            $query->where('date', now()->toDateString());
        })->get();

        if ($tablesDisponibles->isEmpty()) {
            return response()->json(['message' => 'Aucune table disponible'], 404);
        }

        return response()->json($tablesDisponibles, 200);
    }
}
