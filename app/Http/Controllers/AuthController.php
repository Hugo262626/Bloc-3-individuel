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
    /**
     * Inscription d'un nouvel utilisateur via API.
     */
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

            // Générer un token JWT avec createToken
            $token = $user->createToken('auth_token')->plainTextToken;

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

    /**
     * Connexion d'un utilisateur via API.
     */
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
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'message' => 'Identifiants invalides',
                ], 401);
            }

            // Récupère l'utilisateur authentifié
            $user = auth()->user();

            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retourner les informations de l'utilisateur connecté.
     */
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

    /**
     * Déconnexion (invalider le token JWT).
     */
    public function logout()
    {
        try {
            auth()->logout(); // Invalide le token courant
            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retourner le profil de l'utilisateur connecté.
     */
    public function profile()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
        return response()->json($user);
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté.
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Validation des données
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string',
            'birth' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Mise à jour uniquement des champs fournis
        if ($request->has('name')) $user->name = $request->input('name');
        if ($request->has('email')) $user->email = $request->input('email');
        if ($request->has('description')) $user->description = $request->input('description');
        if ($request->has('birth')) $user->birth = $request->input('birth');
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $user->photo = '/uploads/' . $filename;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $user,
        ]);
    }
}
