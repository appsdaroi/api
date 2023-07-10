<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Playpix_Extract;
use App\Models\Playpix_Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlaypixController extends Controller
{
    public function index()
    {
        $users = Playpix_Balance::select('user_id', 'username', 'balance', 'bank', 'playpix_balances.created_at', 'playpix_balances.updated_at')
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
        $extracts = Playpix_Extract::select('id', 'quotes', 'value', 'created_at')
            ->where('user_id', '=', $user_id)
            ->get();

        $balance = Playpix_Balance::select('balance', 'bank')
            ->where('user_id', '=', $user_id)
            ->first();

        if ($balance && $extracts) {
            return [
                "status" => 200,
                "response" => [
                    "user"=> $balance,
                    "extracts" => $extracts
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
            "bank"  => "required"
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Playpix_Balance::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'user_id' => $request->post()["user_id"],
                'balance' => $request->post()["balance"],
                'bank' => $request->post()["bank"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, Playpix_Balance $playpix)
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

        $playpix->update($request->all());

        return [
            "status" => 200,
            "data" => $playpix,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy($user_id)
    {
        $balance = Playpix_Balance::where('user_id', $user_id)->delete();
        $extracts = Playpix_Extract::where('user_id', $user_id)->delete();

        return [
            "status" => 200,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
