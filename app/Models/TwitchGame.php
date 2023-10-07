<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitchGame extends Model
{
    protected $table = 'twitch_games';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'game_id',
        'title',
        'box_art_url',
    ];
}
