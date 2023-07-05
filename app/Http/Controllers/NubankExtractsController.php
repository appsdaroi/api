<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nubank_Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class NubankExtractsController extends Controller
{
    public function index($user_id)
    {
        $extracts = Nubank_extract::select('id', 'data', 'remetente', 'valor', 'tipo')
            ->where('user_id', '=', $user_id)
            ->get();

        if ($extracts) {
            return [
                "status" => 200,
                "response" => $extracts
            ];
        }

        return [
            "status" => 500,
            "response" => "Erro ao listar extratos do usuário"
        ];
    }

    public function store(Request $request, $user_id)
    {
        $validator = Validator::make($request->post(), [
            "valor"  => "required",
            "data"  => "required",
            "tipo"  => "required",
            "remetente"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Nubank_extract::Create([
                'user_id' => $user_id,
                'valor' => $request->post()["valor"],
                'data' => $request->post()["data"],
                'tipo' => $request->post()["tipo"],
                'remetente' => $request->post()["remetente"]
        ]);

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function destroy($user_id, Nubank_extract $extract)
    {
        $extract->delete();

        return [
            "status" => 200,
            "data" => $extract,
            "msg" => "Extrato excluído com sucesso"
        ];
    }
}
