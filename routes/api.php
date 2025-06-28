<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\TypePaiementController;
use App\Http\Controllers\ProduitCommandeController;
use App\Http\Controllers\Auth\GestionnaireAuthController;

// Authentification de base
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:gestionnaire')->group(function () {

    //Route::apiResource('type_paiements', TypePaiementController::class,);

    Route::post('/gestionnaire/logout', [GestionnaireAuthController::class, 'logout']);

});

// Groupes de routes nécessitant une authentification
Route::middleware('auth:sanctum')->group(function () {
    // Commandes et réservations protégées
    Route::post('/commandes', [CommandeController::class, 'store']);
    //Route::post('/reservations', [ReservationController::class, 'store']); // Protection ajoutée ici
    Route::get('/client/commandes', [CommandeController::class, 'getClientCommandes'])->name('client.commandes');
    Route::get('/client/reservations',[ReservationController::class, 'getReservationsByClient']);
    Route::get('/client/compte', [ClientController::class, 'show']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);

    Route::apiResource('type_paiements', TypePaiementController::class,);
    Route::apiResource('paiements', PaiementController::class,);
    Route::apiResource('reservations', ReservationController::class,);
 // Vérification de la disponibilité d'une table
    Route::post('/reservations/verifier-disponibilite', [ReservationController::class, 'verifierDisponibilite']);
     // Récupérer les tables disponibles
    Route::get('/tables-disponibles', [ReservationController::class, 'getTablesDisponibles']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


// Routes d'enregistrement et de connexion pour les clients
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes pour les gestionnaires
Route::post('/gestionnaire/register', [GestionnaireAuthController::class, 'register']);
Route::post('/gestionnaire/login', [GestionnaireAuthController::class, 'login']);

// Autres ressources
Route::apiResource('clients', ClientController::class);
Route::apiResource('produits', ProduitController::class);
Route::apiResource('produit_commandes', ProduitCommandeController::class);
Route::apiResource('type_paiements', TypePaiementController::class);
Route::apiResource('tables', TableController::class);
Route::get('/paiements', [PaiementController::class, 'index']);
Route::get('/commandes', [CommandeController::class, 'getAllCommandes']);
Route::patch('/commandes/{id}', [CommandeController::class, 'updateEtat']);
Route::post('/commandes/{commandeId}/payer', [CommandeController::class, 'payerCommande']);
Route::patch('/tables/{id}/status', [TableController::class, 'updateStatus']);
Route::get('/statistiques/rapport', [StatistiquesController::class, 'getRapport']);
Route::put('/commandes/{id}/annuler', [CommandeController::class, 'annuler']);
Route::post('/paiements/callback', [PaiementController::class, 'handleCallback']);
Route::get('/commande/{commandeId}/succes', [CommandeController::class, 'paiementSuccess'])->name('commande.success');
Route::get('/commande/{commandeId}/annuler', function () {
    return response()->json(['message' => 'Paiement annulé.']);
})->name('commande.cancel');
Route::post('/commandes/callback', [CommandeController::class, 'handleCallback']);


// Supprimez l'accès public pour la route des réservations
// Route::apiResource('reservations', ReservationController::class);
