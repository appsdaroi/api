<?php

namespace App\Http\Controllers;

use App\Models\Betano_Saques;
use App\Models\Betano_User;
use App\Models\IGMoney_Post;
use App\Models\IGMoney_Post_Like;
use App\Models\IGMoney_User;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class IgMoneyAdminController extends Controller
{
    public function index()
    {
        $lastId = request('last_id');
        $limit = request('limit', 10);
        $posts = IGMoney_Post::query()
            ->with('likes')
            ->select('id', 'username', 'src')
            ->when($lastId, function ($query) use ($lastId) {
                return $query->where('id', '<', $lastId);
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($post) {
                $likes = $post->likes()->count();
                $like_text = $likes === 0? 'Ninguém curtiu ainda.': null;
                $like_text = $likes === 1 ? '1 pessoa curtiu.': $like_text;
                $like_text = $likes > 1 ? $likes . ' pessoas curtiram.': $like_text;
                $username = strpos($post->username, '@')? $post->username: '@' . $post->username;
                $src = url('/posts/' . $post->src);
                return [
                    "id" => $post->id,
                    "image" => $src,
                    "username" => $username,
                    "likes" => $likes,
                    "like_text" => $like_text,
                    "liked_by_me" => false,
                ];
            });
        return [
            'status' => 200,
            'response' => $posts,
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
