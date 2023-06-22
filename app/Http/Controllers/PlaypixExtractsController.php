<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Playpix_Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class PlaypixExtractsController extends Controller
{
    public function index($user_id)
    {
        $extracts = Playpix_Extract::select('id', 'quotes', 'value', 'date')
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
            "quotes"  => "required",
            "date"  => "required",
        ]);

        if ($validator->fails()) {
            return response(
                $validator->errors(),
                400
            );
        }

        $user = Playpix_Extract::Create([
                'user_id' => $user_id,
                'value' => $request->post()["value"],
                'date' => $request->post()["date"],
                'quotes' => $request->post()["quotes"],
        ]);

        return [
            "status" => 200,
            "data" => $user
        ];
    }

    public function destroy($user_id, Playpix_Extract $extract)
    {
        $extract->delete();

        return [
            "status" => 200,
            "data" => $extract,
            "msg" => "Extrato excluído com sucesso"
        ];
    }
}
