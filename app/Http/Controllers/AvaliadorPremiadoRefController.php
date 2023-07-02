<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvaliadorPremiado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AvaliadorPremiadoRefController extends Controller
{
    public function show($ref)
    {
        $user = AvaliadorPremiado::select('user_id', 'users.username', 'ref_balance')
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'avaliador_premiados.user_id');
            })
            ->where('ref', '=', $ref)
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

    public function update(Request $request, AvaliadorPremiado $ref)
    {
        $validator = Validator::make($request->all(), [
            "ref_balance"  => "required"
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $ref->update([
            'ref_balance' => $request->all()["ref_balance"],
        ]);

        return [
            "status" => 200,
            "data" => $ref,
            "msg" => "Usuário atualizado com sucesso"
        ];
    }
}
