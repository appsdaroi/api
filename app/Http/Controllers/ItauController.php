<?php

namespace App\Http\Controllers;

use App\Models\Itau_Balance;
use App\Models\Itau_Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ItauController extends Controller
{
    public function index(Request $request)
    {
        $users = Itau_balance::select('user_id', 'username', 'balance', 'itau_balances.created_at', 'itau_balances.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'itau_balances.user_id');
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

    public function show($user_id, Request $request)
    {

        $balance = Itau_balance::select('balance')
            ->where('user_id', '=', $user_id)
            ->first();

        if (isset($request->all()["withExtracts"])) {
            $extracts = Itau_extract::select('id', 'date', 'value', 'type')
            ->where('user_id', '=', $user_id)
            ->get();

            if ($balance && $extracts) {
                return [
                    "status" => 200,
                    "response" => [
                        "balance"=> $balance->balance,
                        "extracts" => $extracts
                    ]
                ];
            }
        }

        if ($balance) {
            return [
                "status" => 200,
                "response" => [
                    "balance"=> $balance->balance,
                ]
            ];
        }

        return [
            "status" => 500,
            "response" => "Erro ao consultar lista de usuários com saldo"
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->post(), [
            "user_id"  => "required",
            "balance"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Itau_balance::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'user_id' => $request->post()["user_id"],
                'balance' => $request->post()["balance"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, Itau_balance $itau)
    {
        $validator = Validator::make($request->all(), [
            "balance"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $itau->update([
            'balance' => $request->all()["balance"],
        ]);

        return [
            "status" => 200,
            "data" => $itau,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }
    
    public function destroy(Itau_balance $itau)
    {
        $itau->delete();

        return [
            "status" => 200,
            "data" => $itau,
            "msg" => "Extrato excluído com sucesso"
        ];
    }
}
