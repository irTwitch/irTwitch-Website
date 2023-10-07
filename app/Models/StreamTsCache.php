<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamTsCache extends Model
{
    protected $table = 'stream_ts_cache';
    protected $primaryKey = 'file_name';
    public $timestamps = false;

    protected $fillable = [
        'file_name',
        'url',
        'downloaded',
        'create_time',
    ];
}
