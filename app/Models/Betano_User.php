<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Betano_User extends Model
{
    use HasFactory;
    protected $table = 'betano_users';
    protected $fillable = [
        'user_id',
        'cpf',
        'saldo_betano',
        'saldo_nubank',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
