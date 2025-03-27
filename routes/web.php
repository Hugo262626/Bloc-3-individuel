<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTAuth;

Route::get('/', function () {
    return view('welcome'); // Remplace index.html par une vue Blade si nécessaire
});

// Routes pour login et register
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Déconnexion
Route::post('/logout', function (Request $request) {
    try {
        // Invalider le token JWT
        JWTAuth::parseToken()->invalidate();
        // Supprimer le token du localStorage côté client (via JavaScript dans app.blade.php)
        return response()->json(['message' => 'Déconnexion réussie', 'redirect' => '/']);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Erreur lors de la déconnexion'], 500);
    }
})->name('logout')->middleware('auth:api');

// Routes protégées par auth:api
Route::middleware(['auth:api'])->get('/app', function () {
    return view('app');
})->name('app');

Route::middleware(['auth:api'])->get('/profile', function () {
    return view('profile');
})->name('profile');
