<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitchToken extends Model
{
    protected $table = 'twitch_tokens';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'token',
        'sec-ch-ua',
        'sec-ch-ua-platform',
        'sec-ch-ua-mobile',
        'Client-Version',
        'User-Agent',
        'Client-Session-Id',
        'X-Device-Id',
        'Client-Id',
    ];
}
