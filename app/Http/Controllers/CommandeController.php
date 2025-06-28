<?php

namespace App\Http\Controllers;

use Paydunya\Setup;
use Paydunya\Checkout\CheckoutInvoice;
use Paydunya\Checkout\Store as PayDunyaStore;
use Paydunya\Checkout\Store;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ProduitCommande;


class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function getAllCommandes()
{
    $commandes = Commande::with(['produits'])->get(); // Inclure les relations nécessaires
    return response()->json($commandes, 200);
}


    public function getClientCommandes()
    {
    $clientId = auth()->id();
    $commandes = Commande::where('client_id', $clientId)->with('produits')->get();

    return response()->json($commandes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Valider les données de la requête
    $request->validate([
        'dateCommande' => 'required|date',
        'etat' => 'required|string',
        'prixTotal' => 'required|numeric',
        'status' => 'required|string',
        'produits' => 'required|array',
        'produits.*.produit_id' => 'required|exists:produits,id',
        'produits.*.quantite' => 'required|integer|min:1',
        'produits.*.prix' => 'required|numeric|min:0',
    ]);

    // Créer la commande en associant le client authentifié
    $commande = new Commande();
    $commande->dateCommande = $request->dateCommande;
    $commande->etat = $request->etat;
    $commande->prixTotal = $request->prixTotal;
    $commande->status = $request->status;
    $commande->client_id = auth()->id(); // Associer le client
    $commande->save();

     // Enregistrer les produits commandés
     foreach ($request->produits as $produit) {
        ProduitCommande::create([
            'commande_id' => $commande->id,
            'produit_id' => $produit['produit_id'],
            'quantite' => $produit['quantite'],
            'prix' => $produit['prix'],
        ]);
    }

// Retourner la commande avec les produits associés
return response()->json([ 'message' => 'Commande ajoutée avec succès', 'commande' => $commande->load('produits'),], 201);}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupère une commande spécifique
        $commande = Commande::find($id);

        if ($commande) {
            return response()->json($commande, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valide les données entrantes
        $request->validate([
            'dateCommande' => 'sometimes|required|date',
            'etat' => 'sometimes|required|string|max:255',
            'prixTotal' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|max:255',
        ]);

        // Récupère et met à jour la commande
        $commande = Commande::find($id);

        if ($commande) {
            $commande->update($request->all());
            return response()->json($commande, Response::HTTP_OK);
        }

        return response()->json(['message' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    public function updateEtat(Request $request, $id)
{
    $request->validate([
        'etat' => 'required|string|in:En attente,En cours,Terminé',
    ]);

    $commande = Commande::findOrFail($id);
    $commande->etat = $request->etat;
    $commande->save();

    return response()->json(['message' => 'État de la commande mis à jour avec succès.', 'commande' => $commande], 200);
}

public function payerCommande(Request $request, $commandeId)
{
    $request->validate([
        'typePaiementId' => 'required|exists:type_paiements,id',
    ]);

    $commande = Commande::findOrFail($commandeId);

    if ($commande->status === 'Payé') {
        return response()->json(['message' => 'Cette commande a déjà été payée.'], 400);
    }

    try {
        // Configurer PayDunya
        Setup::setMasterKey(config('paydunya.master_key'));
        Setup::setPrivateKey(config('paydunya.private_key'));
        Setup::setToken(config('paydunya.token'));
        Setup::setMode(config('paydunya.mode'));

        // Configurer les informations de la boutique
        PayDunyaStore::setName(config('paydunya.store_name'));
        PayDunyaStore::setTagline(config('paydunya.store_tagline'));
        PayDunyaStore::setPhoneNumber(config('paydunya.store_phone'));
        PayDunyaStore::setPostalAddress(config('paydunya.store_address'));
        PayDunyaStore::setWebsiteUrl(config('paydunya.store_website_url'));
        PayDunyaStore::setLogoUrl(config('paydunya.store_logo_url'));

        // Créer une facture PayDunya
        $invoice = new CheckoutInvoice();

        $prixTotal = (float) $commande->prixTotal;
        $invoice->addItem("Commande #$commandeId", 1, $prixTotal, $prixTotal, "Paiement pour commande.");
        $invoice->setTotalAmount($prixTotal);

        $client = $commande->client;
        $invoice->addCustomData("customer_name", $client->nom);
        $invoice->addCustomData("customer_email", $client->email);
        $invoice->addCustomData("customer_phone", $client->telephone);

        // Définir les URLs
        $invoice->setReturnUrl(route('commande.success', ['commandeId' => $commandeId]));
        $invoice->setCancelUrl(route('commande.cancel', ['commandeId' => $commandeId]));   // Annulation
        $invoice->setCallbackUrl(env('PAYDUNYA_CALLBACK_URL'));                           // Callback

        // Créer la facture et rediriger
        if ($invoice->create()) {
            Paiement::create([
                'datePaiement' => now(),
                'montant' => $prixTotal,
                'typePaiementId' => $request->typePaiementId,
                'commande_id' => $commande->id,
                'paydunya_invoice_url' => $invoice->getInvoiceUrl(),
            ]);

            return response()->json([
                'message' => 'Redirection vers PayDunya pour le paiement.',
                'redirect_url' => $invoice->getInvoiceUrl(),
            ]);
        } else {
            return response()->json(['message' => $invoice->response_text], 400);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


public function paiementSuccess(Request $request, $commandeId)
{
    try {
        $commande = Commande::findOrFail($commandeId);

        // Vérifiez si la commande est déjà payée
        if ($commande->status === 'Payé') {
            return redirect()->away('http://localhost:4200/public_page/mes_commandes?message=Paiement déjà validé');
        }

        // Mettez à jour le statut
        $commande->status = 'Payé';
        $commande->save();

        // Redirigez vers l'application Angular avec un message
        return redirect()->away('http://localhost:4200/public_page/mes_commandes?message=Paiement réussi');
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


public function handleCallback(Request $request)
{
    $data = $request->all(); // Récupérez toutes les données de la réponse
    try {
        // Rechercher le paiement par l'URL de la facture PayDunya
        $paiement = Paiement::where('paydunya_invoice_url', $data['invoice_url'])->first();

        if ($paiement) {
            $commande = $paiement->commande;
            $commande->status = 'Payé';
            $commande->save();

            return response()->json(['message' => 'Paiement mis à jour avec succès.']);
        }

        return response()->json(['message' => 'Aucune commande trouvée.'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprime une commande
        $commande = Commande::find($id);

        if ($commande) {
            $commande->delete();
            return response()->json(['message' => 'Commande supprimée avec succès'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    public function annuler(Request $request, $id)
{
    $commande = Commande::find($id);

    if (!$commande) {
        return response()->json(['message' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
    }

    if ($commande->etat === 'Annulé') {
        return response()->json(['message' => 'Commande déjà annulée'], Response::HTTP_BAD_REQUEST);
    }

    // Mettre à jour l'état de la commande
    $commande->etat = 'Annulé';
    $commande->status = 'Annulé';
    $commande->save();

    return response()->json(['message' => 'Commande annulée avec succès'], Response::HTTP_OK);
}
}
