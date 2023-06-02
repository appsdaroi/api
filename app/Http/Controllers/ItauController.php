<?php

namespace App\Http\Controllers;

use App\Models\Itau_balance;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class ItauController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->get('user_id');

        $balance = Itau_balance::select('balance')
            ->where('id', $user_id)
            ->get()
            ->first();

        if ($balance) {
            return [
                "status" => 200,
                "response" => $balance
            ];
        }

        return [
            "status" => 500,
            "response" => "Usuário não encontrado"
        ];
    }
}
