<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Gestionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GestionnaireAuthController extends Controller
{
    // Inscription
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:gestionnaires',
            'password' => 'required|string|min:8',
            'matricule' => 'required|string|max:50|unique:gestionnaires',
        ]);

        $gestionnaire = Gestionnaire::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'matricule' => $request->matricule,
        ]);

        return response()->json(['message' => 'Inscription réussie'], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $gestionnaire = Gestionnaire::where('email', $request->email)->first();


        if (!$gestionnaire || !Hash::check($request->password, $gestionnaire->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $gestionnaire->createToken('auth_token')->plainTextToken;


        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
