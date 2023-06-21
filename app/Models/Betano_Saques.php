<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Betano_Saques extends Model
{
    use HasFactory;
    protected $table = 'betano_saques';
    protected $fillable = [
        'user_id',
        'transicao_id',
        'valor',
        'remetente',
        'saldo_atual_betano',
        'saldo_atual_nubank',
        'data',
        'tipo'
    ];
}
