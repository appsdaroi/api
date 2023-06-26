<?php

namespace App\Http\Controllers;

use App\Models\IGMoney_Post;
use App\Models\IGMoney_User;

class IgMoneyAdminController extends Controller
{
    public function index()
    {
        $limit = request('limit', 10);
        $page = request('page', 1);
        $users = IGMoney_User::query()->with('user')->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        $users->getCollection()->transform(function ($userIGMoney) {
            $createdAt = ($date = optional($userIGMoney->user)->created_at)? $date->format('d/m/Y H:i:s'): null;
            $updatedAt = ($date = optional($userIGMoney->user)->updated_at)? $date->format('d/m/Y H:i:s'): null;
            return [
                "id" => $userIGMoney->user->id,
                "username" => $userIGMoney->user->username,
                "created_at" => $createdAt,
                "updated_at" => $updatedAt,
                "saldo" => $userIGMoney->saldo,
            ];
        });
        return [
            'status' => 200,
            'response' => $users,
        ];
    }

    public function store()
    {
        try {
            $payload = request()->validate([
                'username' => 'required|string',
                'src' => 'required|string',
            ], [
                'username.required' => 'O campo username é obrigatório.',
                'src.required' => 'O campo src é obrigatório.',
            ]);
        } catch (\Throwable $th) {
            return [
                'status' => 400,
                'response' => $th->errors(),
            ];
        }
        $post = IGMoney_Post::create($payload);
        return [
            'status' => 200,
            'response' => $post,
        ];
    }

    public function update(int $postId)
    {
        if (!($post = IGMoney_Post::query()->find($postId))) {
            return [
                'status' => 404,
                'response' => 'Post não encontrado.',
            ];
        }
        try {
            $payload = request()->validate([
                'username' => 'nullable|string',
                'src' => 'nullable|string',
            ]);
        } catch (\Throwable $th) {
            return [
                'status' => 400,
                'response' => $th->errors(),
            ];
        }
        if (empty($payload['username']) && empty($payload['src'])) {
            return [
                'status' => 400,
                'response' => 'Nenhum campo foi enviado.',
            ];
        }
        $values = [];
        if (!empty($payload['username'])) {
            $values['username'] = $payload['username'];
        }
        if (!empty($payload['src'])) {
            $values['src'] = $payload['src'];
        }
        $post->update($values);
        return [
            'status' => 200,
            'response' => $post,
        ];
    }

    public function destroy(int $postId)
    {
        if (!($post = IGMoney_Post::query()->find($postId))) {
            return [
                'status' => 404,
                'response' => 'Post não encontrado.',
            ];
        }
        $post->delete();
        return [
            'status' => 200,
            'response' => 'Post deletado com sucesso.',
        ];
    }

}
