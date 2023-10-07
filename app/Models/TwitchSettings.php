<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitchSettings extends Model
{
    protected $table = 'twitch_settings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tw_token',
        'tw_expire',
    ];
}
