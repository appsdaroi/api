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
            ->leftJoin('users', function ($join) {
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
        $extracts = Playpix_extract::select('id', 'quotes', 'value', 'created_at')
            ->where('user_id', '=', $user_id)
            ->get();

        $balance = Playpix_balance::select('balance')
            ->where('user_id', '=', $user_id)
            ->first();

        if ($balance && $extracts) {
            return [
                "status" => 200,
                "response" => [
                    "balance"=> $balance->balance,
                    "extracts" => $extracts
                ]
            ];
        }

        return [
            "status" => 500,
            "response" => "Erro ao consultar lista de usuários com saldo"
        ];
    }

    public function destroy(Playpix_extract $playpix)
    {
        $playpix->delete();

        return [
            "status" => 200,
            "data" => $playpix,
            "msg" => "User deleted successfully"
        ];
    }
}
