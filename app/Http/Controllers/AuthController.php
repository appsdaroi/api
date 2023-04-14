<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');

        $userData = User::select('id', 'username')
        ->where('username', $username)
        ->where('password', $password)
        ->get()
        ->first();

        if($userData) {
            return [
                "status" => 200,
                "response" => $userData
            ];
        }

        return [
            "status" => 500,
            "response" => "Usuário não existe ou senha incorreta"
        ];
    }
}
