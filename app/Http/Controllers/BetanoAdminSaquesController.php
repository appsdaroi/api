<?php

namespace App\Http\Controllers;

use App\Models\Betano_Saques;
use App\Models\Betano_User;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BetanoAdminSaquesController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $saques = Betano_Saques::query()->with('user', 'userBetano')->paginate($limit, ['*'], 'page', $page);
        $saques->getCollection()->transform(function ($betanoSaque) {
            $betanoSaque = $betanoSaque->toArray();
            $user = $betanoSaque['user'];
            foreach ($betanoSaque['user_betano'] as $key => $value) {
                if ($key === 'id' || $key === 'user_id') {
                    continue;
                }
                $user[$key] = $value;
            }
            $betanoSaque['user'] = $user;
            unset($betanoSaque['user_betano']);
            return $betanoSaque;
        });
        return [
            'status' => 200,
            'response' => $saques,
        ];
    }

    public function show(int $id)
    {
        $user = User::query()->where('id', $id)->first();
        if (!$user) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $page = request('page', 1);
        $limit = request('limit', 10);
        $saques = Betano_Saques::query()->where('user_id', $id)->orderByDesc('id')->paginate($limit, ['*'], 'page', $page);
        return [
            'status' => 200,
            'response' => $saques,
        ];
    }

    public function store()
    {
        try {
            $saque = request()->validate([
                'user_id' => 'required|integer',
                'valor' => 'nullable|numeric',
                'tipo' => 'required|string|in:betano,nubank',
                'data' => 'nullable|date_format:Y-m-d H:i:s',
                'remetente' => 'nullable|string',
            ], [
                'user_id.required' => 'O campo usuário é obrigatório',
                'user_id.integer' => 'O campo usuário deve ser um número inteiro',
                'saldo_betano.numeric' => 'O campo saldo betano deve ser um número',
                'saldo_nubank.numeric' => 'O campo saldo nubank deve ser um número',
                'date.date_format' => 'O campo data deve ser uma data válida: Y-m-d H:i:s',
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 400,
                'response' => $e->getMessage(),
            ];
        }
        $userId = $saque['user_id'];
        $userBetano = Betano_User::query()->where('user_id', $userId)->select([
            'cpf', 'saldo_betano', 'saldo_nubank',
        ])->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $valorAtualBetano = $userBetano->saldo_betano ?? 0;
        $valorAtualNubank = $userBetano->saldo_nubank ?? 0;
        $valorBetano = $saque['tipo'] === 'betano'? ($saque['valor'] ?? $valorAtualBetano) : $valorAtualBetano;
        $valorNubank = $saque['tipo'] === 'nubank'? ($saque['valor'] ?? $valorAtualNubank) : $valorAtualNubank;
        $betanoSaque = Betano_Saques::query()->create([
            'user_id' => $userId,
            'transicao_id' => random_int(1000000000, 9999999999),
            'valor' => $saque['valor'] ?? 0,
            'data' => $saque['data'] ?? Carbon::now()->format('Y-m-d H:i:s'),
            'tipo' => $saque['tipo'],
            'remetente' => $saque['remetente'] ?? 'OKTO PAGAMENTOS S.A',
            'saldo_atual_betano' => $valorBetano ?? 0,
            'saldo_atual_nubank' => $valorNubank ?? 0,
        ]);
        $user = (object) User::query()->where('id', $userId)->first()->toArray();
        $betanoSaque->user = $user;
        return [
            'status' => 200,
            'response' => $betanoSaque,
        ];
    }

    public function update(int $saqueId)
    {
        try {
            $payload = request()->validate([
                'data' => 'nullable|date_format:Y-m-d H:i:s',
                'remetente' => 'nullable|string',
                'valor' => 'nullable|numeric',
                'tipo' => 'nullable|string|in:betano,nubank',
                'saldo_atual_betano' => 'nullable|numeric',
                'saldo_atual_nubank' => 'nullable|numeric',
            ], [
                'date.date_format' => 'O campo data deve ser uma data válida: Y-m-d H:i:s',
                'tipo.in' => 'O campo tipo deve ser betano ou nubank',
                'valor.numeric' => 'O campo valor deve ser um número',
                'saldo_atual_betano.numeric' => 'O campo saldo betano deve ser um número',
                'saldo_atual_nubank.numeric' => 'O campo saldo nubank deve ser um número',
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 400,
                'response' => $e->getMessage(),
            ];
        }
        $saque = Betano_Saques::query()->where('id', $saqueId)->first();
        if (!$saque) {
            return [
                'status' => 404,
                'response' => 'Saque não encontrado',
            ];
        }
        $userId = $saque->user_id;
        $userBetano = Betano_User::query()->where('user_id', $userId)->select([
            'cpf', 'saldo_betano', 'saldo_nubank',
        ])->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        if (!empty($payload['tipo']) && (empty($payload['valor']) || !is_numeric($payload['valor']))) {
            return [
                'status' => 400,
                'response' => 'O campo valor é obrigatório e deve ser um número',
            ];
        }
        if (!empty($payload['tipo']) && (empty($payload['saldo_atual_betano']) && empty($payload['saldo_atual_nubank']))) {
            return [
                'status' => 400,
                'response' => 'O campo saldo atual betano ou saldo atual nubank é obrigatório',
            ];
        }
        if (!empty($payload['tipo'])) {
            $valorAtualBetano = $userBetano->saldo_betano ?? 0;
            $valorAtualNubank = $userBetano->saldo_nubank ?? 0;
            $valorBetano = $payload['tipo'] === 'betano'? ($payload['valor'] ?? $valorAtualBetano) : $valorAtualBetano;
            $valorNubank = $payload['tipo'] === 'nubank'? ($payload['valor'] ?? $valorAtualNubank) : $valorAtualNubank;
            Betano_User::query()->where('user_id', $userId)->update([
                'saldo_betano' => $valorBetano,
                'saldo_nubank' => $valorNubank,
            ]);
            $saque->update($payload);
        }
        $payloadToUpdate = [];
        $columns = ['data', 'remetente', 'valor'];
        foreach ($columns as $column) {
            if (!empty($payload[$column])) {
                $payloadToUpdate[$column] = $payload[$column];
            }
        }
        if (!empty($payloadToUpdate)) {
            $saque->update($payloadToUpdate);
        }
        $user = (object) User::query()->where('id', $userId)->first()->toArray();
        $saque->user = $user;
        return [
            'status' => 200,
            'response' => $saque,
        ];
    }

    public function destroy(int $saqueId)
    {
        $saque = Betano_Saques::query()->where('id', $saqueId)->first();
        if (!$saque) {
            return [
                'status' => 404,
                'response' => 'Saque não encontrado',
            ];
        }
        $userId = $saque->user_id;
        $userBetano = Betano_User::query()->where('user_id', $userId)->select([
            'cpf', 'saldo_betano', 'saldo_nubank',
        ])->first();
        if (!$userBetano) {
            return [
                'status' => 404,
                'response' => 'Usuário não encontrado',
            ];
        }
        $saque->delete();
        return [
            'status' => 200,
            'response' => 'Saque excluído com sucesso',
        ];
    }

}
