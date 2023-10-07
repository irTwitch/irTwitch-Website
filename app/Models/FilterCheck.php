<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterCheck extends Model
{
    protected $table = 'filter_check';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'isLatestDomainOk',
        'isLatestDomainAccessible',
        'date_update',
    ];
}
