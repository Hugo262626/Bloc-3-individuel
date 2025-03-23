<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validation des champs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'description' => $request->description,
            'email' => $request->email,
            'birth' => $request->birth,
            'password' => Hash::make($request->password),
        ]);

        // Connexion automatique après inscription
        Auth::login($user);
        return redirect()->route('app');
    }
    public function login(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Authentifier l'utilisateur
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Connexion réussie
            return redirect()->route('home'); // Redirige vers la page d'accueil ou une autre page
        } else {
            // Échec de la connexion
            return back()->withErrors(['email' => 'Identifiants incorrects.']);
        }
    }

}
