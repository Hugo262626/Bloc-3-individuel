<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire d'inscription (web).
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Afficher le formulaire de connexion (web).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Inscription d'un nouvel utilisateur (web ou API).
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'photo' => 'nullable|image|max:2048',
        ]);

        try {
            // Gestion de l'upload de la photo
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('uploads', 'public');
                $photoPath = '/storage/' . $photoPath;
            }

            // Création de l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'description' => $request->description,
                'email' => $request->email,
                'birth' => $request->birth,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'year' => $request->year,
                'password' => Hash::make($request->password),
                'photo' => $photoPath,
            ]);

            if ($request->expectsJson()) {
                // API: Générer un token JWT
                $token = JWTAuth::fromUser($user);
                return response()->json([
                    'message' => 'Inscription réussie',
                    'token' => $token,
                    'user' => $user,
                ], 201);
            }

            // Web: Connecter automatiquement l'utilisateur
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
            return redirect()->route('app')->with('success', 'Inscription réussie');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Erreur lors de l’inscription',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l’inscription']);
        }
    }

    /**
     * Connexion d'un utilisateur (web ou API).
     */
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        \Log::info('Tentative de connexion avec email: ' . $request->email);

        $credentials = $request->only('email', 'password');

        try {
            if ($request->expectsJson()) {
                // API: Authentification avec JWT
                if (!$token = JWTAuth::attempt($credentials)) {
                    \Log::warning('Échec JWTAuth pour email: ' . $request->email);
                    return response()->json([
                        'success' => false,
                        'message' => 'Identifiants invalides',
                    ], 401);
                }

                $user = Auth::guard('api')->user();
                \Log::info('Connexion API réussie pour email: ' . $request->email . ', User ID: ' . $user->id);

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo,
                    ],
                ], 200);
            }

            // Web: Authentification avec sessions
            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::guard('web')->user();
                \Log::info('Connexion web réussie pour email: ' . $request->email . ', User ID: ' . $user->id);
                \Log::info('Session ID après régénération: ' . $request->session()->getId());
                return redirect()->route('app')->with('success', 'Connexion réussie');
            }

            \Log::warning('Échec Auth::attempt pour email: ' . $request->email);
            return redirect()->back()->withErrors(['email' => 'Identifiants invalides']);
        } catch (JWTException $e) {
            \Log::error('Erreur JWT: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du token',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->withErrors(['email' => 'Erreur lors de la connexion']);
        } catch (\Exception $e) {
            \Log::error('Erreur générale lors de la connexion: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur inattendue lors de la connexion',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->withErrors(['email' => 'Erreur inattendue lors de la connexion']);
        }
    }

    /**
     * Déconnexion (web ou API).
     */
    public function logout(Request $request)
    {
        try {
            if ($request->expectsJson()) {
                // API: Invalider le token JWT
                JWTAuth::parseToken()->invalidate();
                return response()->json(['message' => 'Déconnexion réussie']);
            }

            // Web: Déconnexion de la session
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home')->with('success', 'Déconnexion réussie');
        } catch (JWTException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Erreur lors de la déconnexion',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return redirect()->route('home')->withErrors(['error' => 'Erreur lors de la déconnexion']);
        }
    }

    /**
     * Retourner le profil de l'utilisateur connecté (API).
     */
    public function profile()
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
        return response()->json($user);
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté (API).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Validation des données
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string',
            'birth' => 'nullable|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Mise à jour des champs
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('description')) {
            $user->description = $request->description;
        }
        if ($request->has('birth')) {
            $user->birth = $request->birth;
        }
        if ($request->has('latitude')) {
            $user->latitude = $request->latitude;
        }
        if ($request->has('longitude')) {
            $user->longitude = $request->longitude;
        }
        if ($request->has('year')) {
            $user->year = $request->year;
        }
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads', 'public');
            $user->photo = '/storage/' . $photoPath;
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

    /**
     * Récupérer le profil de l'utilisateur connecté (web).
     */
    public function getWebProfile(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            \Log::error('Erreur getWebProfile: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération du profil'], 500);
        }
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté (web).
     */
    public function updateWebProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'birth' => 'nullable|date',
                'photo' => 'nullable|image|max:2048',
            ]);

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('description')) {
                $user->description = $request->description;
            }
            if ($request->has('birth')) {
                $user->birth = $request->birth;
            }
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('uploads', 'public');
                $user->photo = '/storage/' . $photoPath;
            }

            $user->save();

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur updateWebProfile: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour du profil'], 500);
        }
    }

    /**
     * Récupérer la liste des utilisateurs (web).
     */
    public function getWebUsers(Request $request)
    {
        try {
            $users = User::select('id', 'name', 'description', 'photo', 'role')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Erreur getWebUsers: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des utilisateurs'], 500);
        }
    }
}
