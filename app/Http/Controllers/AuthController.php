<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = User::where('username', $username)->first();

        if ($user) {
            if (Hash::check($password, $user->password)) {
                return [
                    "status" => 200,
                    "response" => $user
                ];
            }

            return [
                "status" => 500,
                "response" => "Senha incorreta"
            ];
        }

        return [
            "status" => 500,
            "response" => "Usuário não existe"
        ];
    }
}
