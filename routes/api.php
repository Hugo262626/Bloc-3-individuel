<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AppController;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

// Vérification du token
Route::get('/check-token', function (Request $request) {
    Log::info('Route /check-token appelée');
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

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées avec le middleware 'auth:api' pour JWT
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
});
Route::get('/users', [AppController::class, 'getUsers'])->name('users');
