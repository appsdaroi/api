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
        return [
            'status' => 200,
            'response' => $users,
        ];
    }

    public function show($id): array
    {
        $user = IGMoney_User::find($id);
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function update($id): array
    {
        $user = IGMoney_User::find($id);
        $user->fill(request()->all());
        $user->save();
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function destroy($id): array
    {
        $user = IGMoney_User::find($id);
        $user->delete();
        return [
            'status' => 200,
            'response' => $user,
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
        $userIGMoney = IGMoney_User::query()->create([
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
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

}
