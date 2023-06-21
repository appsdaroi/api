<?php

namespace App\Http\Controllers;

class GptMinesController extends Controller
{
    public function login()
    {
        $login = request('login');
        $password = request('password');
        if (!$login || !$password) {
            return response()->json(['error' => 'Você precisa passar um login e senha válidos!'], 400);
        }
    }
}
