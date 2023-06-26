<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IGMoney_User extends Model
{
    use HasFactory;
    protected $table = 'igmoney_users';
    protected $fillable = [
        'user_id', 'banco', 'saldo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
