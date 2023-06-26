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

class IgMoneyController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $lastId = request('last_id');
        $limit = request('limit', 10);
        $postsIdsLikeds = IGMoney_Post_Like::query()->where('user_id', $userId)->pluck('post_id')->toArray();
        $posts = IGMoney_Post::query()
            ->with('likes')
            ->whereNotIn('id', $postsIdsLikeds)
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
        $userId = auth()->id();
        $userIGMoney = IGMoney_User::query()->where('user_id', $userId)->first();
        if (!$userIGMoney) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $likeds = request('likeds', []);
        $notLikeds = request('notLikeds', []);
        $saldo = $userIGMoney->saldo + count($likeds) - count($notLikeds);
        $saldo = $saldo < 0 ? 0 : $saldo;
        $userIGMoney->update(['saldo' => $saldo]);
        if (count($notLikeds)) {
            IGMoney_Post_Like::query()
                ->where('user_id', $userId)
                ->whereIn('post_id', $notLikeds)
                ->delete();
        }
        if (count($likeds)) {
            IGMoney_Post_Like::query()
                ->insert(
                    collect($likeds)->map(function ($post_id) use ($userId) {
                        return [
                            'user_id' => $userId,
                            'post_id' => $post_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    })->toArray()
                );
        }
        $user = (object) User::query()->where('id', auth()->user()->id)->first()->toArray();
        foreach ($userIGMoney->toArray() as $key => $value) {
            $user->$key = $value;
        }
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function profile()
    {
        $user = (object) User::query()->where('id', auth()->user()->id)->first()->toArray();
        $userIGMoney = IGMoney_User::query()->where('user_id', $user->id)->select('banco', 'saldo')->first();
        if (!$userIGMoney) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        foreach ($userIGMoney->toArray() as $key => $value) {
            $user->$key = $value;
        }
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function saques()
    {
        $userId = auth()->id();
        $userIGMoney = IGMoney_User::query()->where('user_id', $userId)->first();
        if (!$userIGMoney) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $valor = filter_var(request('valor'), FILTER_SANITIZE_NUMBER_FLOAT);
        if (!$valor) {
            return [
                'status' => 400,
                'response' => 'Valor inválido',
            ];
        }
        if ($valor > $userIGMoney->saldo) {
            return [
                'status' => 400,
                'response' => 'Saldo insuficiente',
            ];
        }
        $userIGMoney->update(['saldo' => $userIGMoney->saldo - $valor]);
        $user = (object) User::query()->where('id', auth()->user()->id)->first()->toArray();
        foreach ($userIGMoney->toArray() as $key => $value) {
            $user->$key = $value;
        }
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

}
