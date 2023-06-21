<?php

namespace App\Http\Controllers;

class GptMinesController extends Controller
{
    public function index(): array
    {
        $game = [
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
        ];
        // Generate four random positions
        $positions = array_rand($game, 4);
        // Set the corresponding values to 1
        foreach ($positions as $position) {
            $subarray = &$game[$position];
            $subkey = array_rand($subarray);
            $subarray[$subkey] = 1;
        }
        return [
            "status" => 200,
            "response" => $game
        ];
    }
}
