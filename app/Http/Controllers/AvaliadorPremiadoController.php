<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvaliadorPremiado_User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AvaliadorPremiadoController extends Controller
{
    public function index()
    {
        $users = AvaliadorPremiado_User::select('avaliador_premiado.id', 'user_id', 'username', 'balance', 'bank', 'avaliador_premiado.created_at', 'avaliador_premiado.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'avaliador_premiado.user_id');
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
        $user = AvaliadorPremiado_User::select('user_id', 'balance', 'bank', 'created_at')
            ->where('user_id', '=', $user_id)
            ->get();

        if (!$user->isEmpty()) {
            return [
                "status" => 200,
                "response" => [
                    "user" => $user[0]
                ]
            ];
        }

        return [
            "status" => 500,
            "response" => "Usuário não encontrado"
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->post(), [
            "user_id"  => "required",
            "balance"  => "required",
            "bank"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = AvaliadorPremiado_User::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'balance' => $request->post()["balance"],
                'bank' => $request->post()["bank"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, AvaliadorPremiado_User $avaliador_premiado)
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

        $avaliador_premiado->update([
            'balance' => $request->all()["balance"],
        ]);

        return [
            "status" => 200,
            "data" => $avaliador_premiado,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy(AvaliadorPremiado_User $avaliador_premiado)
    {
        $avaliador_premiado->delete();

        return [
            "status" => 200,
            "data" => $avaliador_premiado,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
