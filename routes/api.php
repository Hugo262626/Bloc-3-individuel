<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AppController;
use Illuminate\Support\Facades\Route;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

Route::get('/check-token', function (Request $request) {
    try {
        // Récupérer l'utilisateur à partir du token
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['message' => 'Token valide', 'user' => $user]);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'Token expiré'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['error' => 'Token invalide'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Token absent'], 401);
    }
})->middleware('auth:api');

// Vérification du token (accessible via auth:api)
Route::get('/check-token', function () {
    try {
        $user = auth()->user();
        return response()->json(['message' => 'Token valide', 'user' => $user]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Token invalide'], 401);
    }
})->middleware('auth:api');


Route::middleware(['auth:api'])->get('/app', function () {
    return response()->file(public_path('app.html'));
})->name('app');

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées avec le middleware 'auth:api' pour JWT
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes protégées avec le middleware 'jwt.auth'
use App\Http\Controllers\AuthController;

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']); // Récupérer le profil
    Route::post('/profile', [AuthController::class, 'updateProfile']); // Mettre à jour le profil
});

// API publique
Route::get('/users', [AppController::class, 'index']);
