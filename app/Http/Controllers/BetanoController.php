<?php

namespace App\Http\Controllers;

use App\Models\Betano_Saques;
use App\Models\Betano_User;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BetanoController extends Controller
{
    public function index()
    {
        $tipo = request('tipo');
        $saques = Betano_Saques::query()->where('user_id', auth()->user()->id)->get();
        $limit = request('limit');
        $saques = $saques? $saques
            ->when($tipo, static function ($query) use ($tipo) {
                return $query->where('tipo', $tipo);
            })
            ->when($limit, static function ($query) use ($limit) {
                return $query->take($limit);
            })->map(static function ($saque) {
                return [
                    'id' => $saque->id,
                    'tipo' => $saque->tipo,
                    'transicao_id' => $saque->transicao_id,
                    'valor' => $saque->valor,
                    'saldo_atual_nubank' => $saque->saldo_atual_nubank,
                    'saldo_atual_betano' => $saque->saldo_atual_betano,
                    'data' => $saque->data,
                    'data_formatada' => $saque->data? Carbon::parse($saque->data)->format('d/m/Y H:i\h'): null,
                    'remetente' => $saque->remetente,
                ];
            })->values()->toArray(): [];
        // sort $saques by date
        usort($saques, static function ($a, $b) {
            return $a['data'] < $b['data'];
        });
        return [
            'status' => 200,
            'response' => $saques,
        ];
    }

    public function profile()
    {
        $user = (object) User::query()->where('id', auth()->user()->id)->first()->toArray();
        $userBetano = Betano_User::query()->where('user_id', $user->id)->select([
            'cpf', 'saldo_betano', 'saldo_nubank',
        ])->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        foreach ($userBetano->toArray() as $key => $value) {
            $user->$key = $value;
        }
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function store()
    {
        try {
            $profile = request()->validate([
                'username' => 'required|unique:users',
                'password' => 'required',
                'cpf' => 'required',
                'saldo_betano' => 'required',
                'saldo_nubank' => 'required',
            ], [
                'username.required' => 'O campo username é obrigatório',
                'username.unique' => 'O username informado já está em uso',
                'password.required' => 'O campo password é obrigatório',
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
        if (User::query()->where('username', $profile['username'])->first()) {
            return [
                'status' => 400,
                'response' => 'O username informado já está em uso',
            ];
        }
        // check password
        if (strlen($profile['password']) < 6) {
            return [
                'status' => 400,
                'response' => 'A senha deve conter no mínimo 6 caracteres',
            ];
        }
        $user = User::query()->create([
            'username' => $profile['username'],
            'password' => bcrypt($profile['password']),
            'api_token' => Str::random(60),
        ]);
        $userBetano = Betano_User::query()->create([
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
        return [
            'status' => 200,
            'response' => $user,
        ];
    }

    public function update()
    {
        $valor = request('valor');
        $userBetano = Betano_User::query()->where('user_id', auth()->id())->select([
            'cpf', 'saldo_betano', 'saldo_nubank',
        ])->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $valorBetano = $userBetano->saldo_betano ?? 0;
        $valorNubank = $userBetano->saldo_nubank ?? 0;
        if ($valorBetano < $valor) {
            return [
                'status' => 400,
                'response' => 'Saldo insuficiente',
            ];
        }
        $valorBetano -= $valor;
        $valorNubank += $valor;
        Betano_User::query()->where('user_id', auth()->id())->update([
            'saldo_betano' => $valorBetano,
            'saldo_nubank' => $valorNubank,
        ]);
        Betano_Saques::query()->create([
            'user_id' => auth()->id(),
            'transicao_id' => random_int(1000000000, 9999999999),
            'valor' => $valor,
            'data' => now(),
            'tipo' => 'nubank',
            'saldo_atual_betano' => $valorBetano,
            'saldo_atual_nubank' => $valorNubank,
        ]);
        $user = (object) auth()->user();
        foreach ($userBetano->toArray() as $key => $value) {
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
