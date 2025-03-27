<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getUsers()
    {
        try {
            $users = User::all();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des utilisateurs', 'error' => $e->getMessage()], 500);
        }
    }
}
