<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IGMoney_Post extends Model
{
    use HasFactory;
    protected $table = 'igmoney_posts';
    protected $fillable = [
        'username',
        'src',
    ];

    public function likes()
    {
        return $this->hasMany(related: IGMoney_Post_Like::class, foreignKey: 'post_id', localKey: 'id');
    }

}
