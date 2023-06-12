<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Itau_Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ItauExtractsController extends Controller
{
    public function index($user_id)
    {
        $extracts = Itau_extract::select('id', 'date', 'title', 'value', 'type')
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
            "value"  => "required",
            "date"  => "required",
            "type"  => "required",
            "title"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Itau_extract::Create([
                'user_id' => $user_id,
                'value' => $request->post()["value"],
                'date' => $request->post()["date"],
                'type' => $request->post()["type"],
                'title' => $request->post()["title"]
        ]);

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function destroy($user_id, Itau_extract $extract)
    {
        $extract->delete();

        return [
            "status" => 200,
            "data" => $extract,
            "msg" => "Extrato excluído com sucesso"
        ];
    }
}
