<?php

namespace App\Http\Controllers;

use App\Models\Betano_Saques;
use App\Models\Betano_User;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BetanoAdminController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $users = Betano_User::query()->with('user')->paginate($limit, ['*'], 'page', $page);
        $users->getCollection()->transform(function ($betanoUser) {
            $betanoUser = $betanoUser->toArray();
            $user = [];
            foreach ($betanoUser as $key => $value) {
                if ($key === 'user') {
                    continue;
                }
                $user[$key] = $value;
            }
            foreach ($betanoUser['user'] as $key => $value) {
                if ($key === 'id') {
                    continue;
                }
                $user[$key] = $value;
            }
            $user['id'] = $betanoUser['user_id'];
            unset($user['user_id']);
            return $user;
        });
        return [
            'status' => 200,
            'response' => $users,
        ];
    }

    public function store()
    {
        try {
            $profile = request()->validate([
                'user_id' => 'required',
                'cpf' => 'required',
                'saldo_betano' => 'required',
                'saldo_nubank' => 'required',
            ], [
                'user_id.required' => 'O campo user_id é obrigatório',
                'cpf.required' => 'O campo cpf é obrigatório',
                'saldo_betano.required' => 'O campo saldo_betano é obrigatório',
                'saldo_nubank.required' => 'O campo saldo_nubank é obrigatório',
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
        $userBetano = Betano_User::query()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'user_id' => $user->id,
            'cpf' => $profile['cpf'],
            'saldo_betano' => $profile['saldo_betano'],
            'saldo_nubank' => $profile['saldo_nubank'],
        ]);
        $user = (object) User::query()->where('id', $user->id)->first()->toArray();
        foreach ($userBetano->toArray() as $key => $value) {
            if ($key === 'user_id') {
                continue;
            }
            $user->$key = $value;
        }
        $user->id = $userBetano->user_id;
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function show(int $userId)
    {
        $userBetano = Betano_User::query()->where('user_id', $userId)->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $user = (object) User::query()->where('id', $userBetano->user_id)->first()->toArray();
        foreach ($userBetano->toArray() as $key => $value) {
            if ($key === 'user_id') {
                continue;
            }
            $user->$key = $value;
        }
        $user->id = $userBetano->user_id;
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function update(int $userId)
    {
        $userBetano = Betano_User::query()->where('user_id', $userId)->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        try {
            $profile = request()->validate([
                'cpf' => 'nullable',
                'saldo_betano' => 'nullable',
                'saldo_nubank' => 'nullable',
            ]);
            $userBetano->update($profile);
            $user = (object) User::query()->where('id', $userBetano->user_id)->first()->toArray();
            foreach ($userBetano->toArray() as $key => $value) {
                if ($key === 'user_id') {
                    continue;
                }
                $user->$key = $value;
            }
            $user->id = $userBetano->user_id;
            return [
                'status' => 200,
                'response' => $user,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 400,
                'response' => $e->getMessage(),
            ];
        }
    }

    public function destroy(int $userId)
    {
        $userBetano = Betano_User::query()->where('user_id', $userId)->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        Betano_Saques::query()->where('user_id', $userId)->delete();
        $userBetano->delete();
        return [
            'status' => 200,
            'response' => 'Usuário deletado com sucesso',
        ];
    }

}
