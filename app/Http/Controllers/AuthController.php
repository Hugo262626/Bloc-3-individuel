<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'description' => 'nullable|string',
            'birth' => 'nullable|date',
        ]);

        try {
            // Création de l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'description' => $request->description,
                'email' => $request->email,
                'birth' => $request->birth,
                'password' => Hash::make($request->password),
            ]);

            // Générer un token JWT
            $token = JWTAuth::fromUser($user);

            // Retourner une réponse JSON avec le token
            return response()->json([
                'message' => 'Inscription réussie',
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l’inscription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Affichage du formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Connexion de l'utilisateur
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            // Tenter de générer un token JWT
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Identifiants invalides',
                ], 401);
            }

            // Retourner le token dans une réponse JSON
            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => auth('api')->user(), // Récupère l'utilisateur authentifié via JWT
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Retourner les informations de l'utilisateur connecté
    public function me(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur', 'error' => $e->getMessage()], 500);
        }
    }

    // Déconnexion (invalider le token JWT)
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
        return response()->json($user);
    }

    // Mettre à jour les informations du profil
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Validation des données
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'description' => 'nullable|string',
            'birth' => 'nullable|date',
            'photo' => 'nullable|image|max:2048', // Image max 2MB
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Mise à jour des champs (seuls ceux fournis dans la requête)
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->description = $request->input('description', $user->description);
        $user->birth = $request->input('birth', $user->birth);

        // Mise à jour du mot de passe si fourni
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Gestion de la photo de profil
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $user->photo = '/uploads/' . $filename;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $user,
        ]);
    }
}
