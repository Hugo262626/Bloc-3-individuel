<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AppController;
use Illuminate\Support\Facades\Auth;

// Authentification
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (utilisation de auth:api pour JWT)
Route::middleware(['auth:api'])->group(function () {
Route::get('/user', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/app', [AppController::class, 'index'])->name('app');  //route protégé
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});


// Page d'accueil
Route::get('/', function () {
return response()->file(public_path('index.html'));
});

// Déconnexion
Route::post('/logout', function () {
Auth::logout();
request()->session()->invalidate();
request()->session()->regenerateToken();
return redirect('/');
})->name('logout');

// API publique
Route::get('/users', [AppController::class, 'index']);
