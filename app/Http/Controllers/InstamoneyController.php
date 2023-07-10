<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instamoney_user;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InstamoneyController extends Controller
{
    public function index()
    {
        $users = Instamoney_user::select('instamoney_users.id', 'user_id', 'username', 'balance', 'ref_balance', 'ref', 'bank', 'instamoney_users.created_at', 'instamoney_users.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'instamoney_users.user_id');
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
        $user = Instamoney_user::select('user_id', 'balance', 'ref_balance', 'ref', 'bank', 'created_at')
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

        $user = Instamoney_user::firstOrCreate(
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

    public function update(Request $request, Instamoney_user $instamoney)
    {
        $instamoney->update([
            'balance' => $request->all()["balance"],
            'ref_balance' => $request->all()["ref_balance"],
            'bank' => $request->all()["bank"],
        ]);

        return [
            "status" => 200,
            "data" => $instamoney,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy(Instamoney_user $instamoney)
    {
        $instamoney->delete();

        return [
            "status" => 200,
            "data" => $instamoney,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
