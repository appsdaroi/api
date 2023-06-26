<?php

namespace App\Http\Controllers;

use App\Models\IGMoney_User;
use App\Models\User;

class IgMoneyAdminUsersController extends Controller
{
    public function index(): array
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        $users = IGMoney_User::paginate($limit, ['*'], 'page', $page);
        $users->getCollection()->transform(function ($user) {
            $user->id = $user->user->id;
            unset($user->user, $user->user_id);
            return $user;
        });
        return [
            'status' => 200,
            'response' => $users,
        ];
    }

    public function show($id): array
    {
        $user = IGMoney_User::query()->where('user_id', $id)->first();
        if (!$user) {
            return [
                'status' => 400,
                'response' => 'Usuário não encontrado',
            ];
        }
        $user->id = $user->user->id;
        unset($user->user, $user->user_id);
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function update($id): array
    {
        $user = IGMoney_User::query()->where('user_id', $id)->first();
        if (!$user) {
            return [
                'status' => 400,
                'response' => 'Usuário não encontrado',
            ];
        }
        $user->fill(request()->all());
        $user->save();
        $user->id = $user->user->id;
        unset($user->user, $user->user_id);
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function destroy($id): array
    {
        $user = IGMoney_User::query()->where('user_id', $id)->first();
        if (!$user) {
            return [
                'status' => 400,
                'response' => 'Usuário não encontrado',
            ];
        }
        $user->delete();
        return [
            'status' => 200,
            'response' => 'Usuário deletado com sucesso'
        ];
    }

    public function store(): array
    {
        try {
            $profile = request()->validate([
                'user_id' => 'required',
                'saldo' => 'required|numeric',
            ], [
                'user_id.required' => 'O campo user_id é obrigatório',
                'saldo.required' => 'O campo saldo é obrigatório',
                'saldo.numeric' => 'O campo saldo deve ser numérico',
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 400,
                'response' => $e->getMessage(),
            ];
        }
        if (!($user = User::query()->where('id', $profile['user_id'])->first())) {
            return [
                'status' => 400,
                'response' => 'Usuário não encontrado',
            ];
        }
        $userIGMoney = IGMoney_User::query()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'user_id' => $user->id,
            'saldo' => $profile['saldo'],
        ]);
        $user = (object) User::query()->where('id', $user->id)->first()->toArray();
        foreach ($userIGMoney->toArray() as $key => $value) {
            if ($key === 'user_id') {
                continue;
            }
            $user->$key = $value;
        }
        $user->id = $userIGMoney->user->id;
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

}
