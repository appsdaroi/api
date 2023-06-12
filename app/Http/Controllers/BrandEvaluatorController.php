<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BrandEvaluator_User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandEvaluatorController extends Controller
{
    public function index()
    {
        $users = BrandEvaluator_User::select('brand_evaluator_users.id', 'user_id', 'username', 'balance', 'bank', 'brand_evaluator_users.created_at', 'brand_evaluator_users.updated_at')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'brand_evaluator_users.user_id');
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
        $user = BrandEvaluator_User::select('user_id', 'balance', 'bank', 'created_at')
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

        $user = BrandEvaluator_User::firstOrCreate(
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

    public function update(Request $request, BrandEvaluator_User $avaliador)
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

        $avaliador->update([
            'balance' => $request->all()["balance"],
        ]);

        return [
            "status" => 200,
            "data" => $avaliador,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }

    public function destroy(BrandEvaluator_User $avaliador)
    {
        $avaliador->delete();

        return [
            "status" => 200,
            "data" => $avaliador,
            "msg" => "Usuário excluído com sucesso"
        ];
    }
}
