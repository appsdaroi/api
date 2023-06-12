<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socialmoney_User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SocialmoneyController extends Controller
{
    public function index()
    {
        $users = Socialmoney_user::select('socialmoney_users.id', 'user_id', 'username', 'balance', 'bank', 'ref', 'socialmoney_users.created_at', 'socialmoney_users.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'socialmoney_users.user_id');
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
        $user = Socialmoney_user::select('user_id', 'balance', 'bank', 'ref', 'created_at')
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

        $user = Socialmoney_user::firstOrCreate(
            [
                'user_id' => $request->post()["user_id"]
            ],
            [
                'ref' => Str::random(10),
                'balance' => $request->post()["balance"],
                'bank' => $request->post()["bank"],
            ]
        );

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function update(Request $request, Socialmoney_user $socialmoney)
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

        $socialmoney->update([
            'balance' => $request->all()["balance"],
        ]);

        return [
            "status" => 200,
            "data" => $socialmoney,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy(Socialmoney_user $socialmoney)
    {
        $socialmoney->delete();

        return [
            "status" => 200,
            "data" => $socialmoney,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
