<?php

use App\Http\Controllers\Api\AppController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Auth;

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// afficher la page de connexion
Route::get('/login', function () {
    return view('login');
})->name('login.form');

// utilisation du formulaire d'inscription (connecté)
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/', function () {
    return response()->file(public_path('index.html'));
});

Route::get('/app', function () {
    return response()->file(public_path('app.html'));
})->name('app');

// déconnexion
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate(); // Invalide la session
    request()->session()->regenerateToken(); // Regénère le token CSRF pour la sécurité
    return redirect('/'); // Redirection après déconnexion
})->name('logout');

//API
Route::get('/users', [AppController::class, 'index']);
