<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamViewers extends Model
{
    protected $table = 'stream_viewers';
    protected $primaryKey = ['user_ip', 'streamer'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_ip',
        'streamer',
        'attempt',
        'check_date',
    ];
}
