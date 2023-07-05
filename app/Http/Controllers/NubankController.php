<?php

namespace App\Http\Controllers;

use App\Models\Nubank_Balance;
use App\Models\Nubank_Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NubankController extends Controller
{
    public function index(Request $request)
    {
        $users = Nubank_balance::select('user_id', 'username', 'saldo', 'nubank_balances.created_at', 'nubank_balances.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'nubank_balances.user_id');
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

        $balance = Nubank_balance::select('saldo', 'username')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'nubank_balances.user_id');
            })
            ->where('user_id', '=', $user_id)
            ->first();

        if (isset($request->all()["withExtracts"])) {
            $extracts = Nubank_extract::select('id', 'data', 'valor', 'tipo')
            ->where('user_id', '=', $user_id)
            ->get();

            if ($balance && $extracts) {
                return [
                    "status" => 200,
                    "response" => [
                        "saldo"=> $balance->saldo,
                        "extracts" => $extracts
                    ]
                ];
            }
        }

        if ($balance) {
            return [
                "status" => 200,
                "response" => [
                    "saldo"=> $balance->saldo,
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
            "saldo"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Nubank_balance::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'user_id' => $request->post()["user_id"],
                'saldo' => $request->post()["saldo"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, Nubank_balance $nubank)
    {
        $validator = Validator::make($request->all(), [
            "saldo"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $nubank->update([
            'saldo' => $request->all()["saldo"],
        ]);

        return [
            "status" => 200,
            "data" => $nubank,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }
    
    public function destroy(Nubank_balance $nubank)
    {
        $nubank->delete();

        return [
            "status" => 200,
            "data" => $nubank,
            "msg" => "Extrato excluído com sucesso"
        ];
    }
}
