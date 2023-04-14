<?php

namespace App\Http\Controllers;

use App\Models\Playpix_extract;
use Illuminate\Http\Request;

class PlaypixController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->get('user_id');

        $extracts = Playpix_extract::select('*')
            ->where('user_id', $user_id)
            ->get();

        if ($extracts) {
            return [
                "status" => 200,
                "response" => $extracts
            ];
        }

        return [
            "status" => 500,
            "response" => "Usuário não tem extratos"
        ];
    }
}
