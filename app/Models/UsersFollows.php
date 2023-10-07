<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersFollows extends Model
{
    protected $table = 'users_follows';
    protected $primaryKey = ['userid', 'streamerid'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['userid', 'streamerid'];
}