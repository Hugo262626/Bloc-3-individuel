<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Affichage du formulaire d'inscription
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Inscription de l'utilisateur
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

        // Générer un token JWT
        $token = JWTAuth::fromUser($user);

        // Rediriger vers la route 'app' avec le token
        return redirect()->route('app')->with('token', $token); // Ajoute le token à la session si nécessaire
    }

    // Affichage du formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login'); // Vue de connexion (assure-toi que tu as une vue 'auth.login')
    }

    // Connexion de l'utilisateur
    public function login(Request $request)
    {
        // Validation des données
        $credentials = $request->only('email', 'password');

        // Tenter de se connecter avec les identifiants fournis
        if ($token = JWTAuth::attempt($credentials)) {
            // Connexion réussie, retour du token JWT
            return redirect()->route('app')->with(['token', $token]); // Redirection vers la page 'app' avec le token

        }

        // Identifiants invalides
        return response()->json([
            'message' => 'Identifiants invalides',
        ], 401);
    }

    // Retourner les informations de l'utilisateur connecté
    public function me()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    // Déconnexion (supprimer le token JWT)
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function profile()
    {
        $user = auth()->user(); // Récupère l'utilisateur connecté
        return response()->json($user);
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user(); // Récupère l'utilisateur connecté

        // Validation des données
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // Image max 2MB
        ]);

        // Mettre à jour les informations
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->description = $request->description ?? $user->description;

        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $user->photo = '/uploads/' . $filename;
        }

        $user->save(); // Sauvegarde les modifications

        return response()->json(['message' => 'Profil mis à jour avec succès', 'user' => $user]);
    }

}
