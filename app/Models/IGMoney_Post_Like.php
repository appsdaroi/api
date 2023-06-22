<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IGMoney_Post_Like extends Model
{
    use HasFactory;
    protected $table = 'igmoney_post_likes';
    protected $fillable = [
        'user_id',
        'post_id',
    ];
}
