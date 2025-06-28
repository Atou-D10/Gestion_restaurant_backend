<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère toutes les tables
        $tables = Table::all();
        return response()->json($tables, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données entrantes
        $request->validate([
            'numeroTable' => 'required|integer|unique:tables,numeroTable',
            'nbrPlace' => 'required|integer|min:1',
        ]);

        // Crée une nouvelle table
        $table = Table::create([
            'numeroTable' => $request->numeroTable,
            'nbrPlace' => $request->nbrPlace,
        ]);

        return response()->json($table, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère une table spécifique
        $table = Table::find($id);

        if ($table) {
            return response()->json($table, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Table non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'numeroTable' => 'sometimes|required|integer|unique:tables,numeroTable,' . $id,
            'nbrPlace' => 'sometimes|required|integer|min:1',
        ]);

        // Récupère et met à jour la table
        $table = Table::find($id);

        if ($table) {
            $table->update($request->all());
            return response()->json($table, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Table non trouvée'], Response::HTTP_NOT_FOUND);
    }


    public function updateStatus($id, Request $request)
{
    $request->validate([
        'status' => 'required|in:disponible,occupée,reservée',
    ]);
    $table = Table::findOrFail($id);
    $table->status = $request->status; // "Occupé" ou "Disponible"
    $table->save();

    return response()->json($table);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime une table
        $table = Table::find($id);

        if ($table) {
            $table->delete();
            return response()->json(['message' => 'Table supprimée avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Table non trouvée'], Response::HTTP_NOT_FOUND);
    }
}
