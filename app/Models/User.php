<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'twitch_username',
        'twitch_userid',
        'twitch_a',
        'twitch_r',
        'twitch_expire',
        'join_date',
        'user_token',
        'token_date',
        'app_token',
        'fcmToken',
        'fcmToken_date',
        'apptoken_date',
    ];
}
