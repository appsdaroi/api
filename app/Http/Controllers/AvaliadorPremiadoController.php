<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvaliadorPremiado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AvaliadorPremiadoController extends Controller
{
    public function index()
    {
        $users = AvaliadorPremiado::select('avaliador_premiados.id', 'user_id', 'username', 'balance', 'ref_balance', 'ref', 'bank', 'avaliador_premiados.created_at', 'avaliador_premiados.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'avaliador_premiados.user_id');
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
        $user = AvaliadorPremiado::select('user_id', 'balance', 'ref_balance', 'ref', 'bank', 'created_at')
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
            "ref_balance"  => "required",
            "bank"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = AvaliadorPremiado::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'ref' => Str::random(10),
                'balance' => $request->post()["balance"],
                'ref_balance' => $request->post()["ref_balance"],
                'bank' => $request->post()["bank"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, AvaliadorPremiado $avaliadorpremiado)
    {
        $avaliadorpremiado->update([
            'balance' => $request->all()["balance"],
            'ref_balance' => $request->all()["ref_balance"],
            'bank' => $request->all()["bank"],
        ]);

        return [
            "status" => 200,
            "data" => $avaliadorpremiado,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy(AvaliadorPremiado $avaliadorpremiado)
    {
        $avaliadorpremiado->delete();

        return [
            "status" => 200,
            "data" => $avaliadorpremiado,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
