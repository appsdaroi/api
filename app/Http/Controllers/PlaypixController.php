<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Playpix_extract;
use App\Models\Playpix_balance;
use Illuminate\Http\Request;

class PlaypixController extends Controller
{
    public function index()
    {
        $users = Playpix_balance::select('user_id', 'username', 'balance', 'playpix_balances.created_at', 'playpix_balances.updated_at')
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'playpix_balances.user_id');
          })
          ->get();

        if ($users) {
            return [
                "status" => 200,
                "response" => $users
            ];
        }

        return [
            "status" => 500,
            "response" => "Erro ao consultar lista de usuários com saldo"
        ];
    }

    public function show($user_id)
    {
        $users = Playpix_extract::select('quotes', 'value')
        ->where('user_id', '=', $user_id)
        ->get();

        if ($users) {
            return [
                "status" => 200,
                "response" => $users
            ];
        }

        return [
            "status" => 500,
            "response" => "Erro ao consultar lista de usuários com saldo"
        ];
    }
}
