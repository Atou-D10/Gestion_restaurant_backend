<?php

return [
    'master_key' => env('PAYDUNYA_MASTER_KEY'),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
    'token' => env('PAYDUNYA_TOKEN'),
    'mode' => env('PAYDUNYA_MODE', 'test'), // Peut être 'live' ou 'test'
    'success_url' => env('PAYDUNYA_SUCCESS_URL', '/api/commandes/success'),
    'cancel_url' => env('PAYDUNYA_CANCEL_URL', '/api/paiements/cancel'),
    'callback_url' => env('PAYDUNYA_CALLBACK_URL', '/api/paiements/callback'),

        // Informations sur la boutique
'store_name' => env('PAYDUNYA_STORE_NAME', 'Nom par défaut'),
'store_tagline' => env('PAYDUNYA_STORE_TAGLINE', 'Tagline par défaut'),
'store_phone' => env('PAYDUNYA_STORE_PHONE', 'Téléphone par défaut'),
'store_postal_address' => env('PAYDUNYA_STORE_ADDRESS', 'Adresse par défaut'),
'store_logo_url' => env('PAYDUNYA_STORE_LOGO_URL', 'URL par défaut du logo'),
'store_website_url' => env('PAYDUNYA_STORE_WEBSITE_URL', 'URL par défaut du site web')

];
