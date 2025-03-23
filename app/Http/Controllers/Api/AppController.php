<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        // Récupérer les utilisateurs avec id, name et description
        $users = User::select('id', 'name', 'description')->get();

        // Retourner les utilisateurs sous forme de JSON
        return response()->json($users);
    }
}
