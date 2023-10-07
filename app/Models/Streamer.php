<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streamer extends Model
{
    protected $table = 'streamers';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'discord_userid',
        'is_relay',
        'twitch_userid',
        'isLive',
        'isBan',
        'stream_start',
        'twitch_display_name',
        'twitch_description',
        'twitch_profile_image_url',
        'twitch_offline_image_url',
        'twitch_account_create',
        'twitch_title',
        'twitch_category_id',
        'twitch_account_type',
        'twitch_data_update',
        'twitch_check_date',
        'twitch_thumbnail_url',
        'quality_url',
        'master_lastcheck',
        'master_playlist',
        'master_playlist_original',
        'playlist_cache',
        'playlist_time',
        'playlist_checksum',
        'subscribed',
        'subscribe_botid',
        'twitch_viewers',
        'stream_viewers',
        'stream_links',
        'add_date',
    ];

    public static function getStreamerByUsername($username)
    {
        return self::where('username', GetCleanInput(strtolower($username)))->first();
    }

    public static function getStreamerByTwitchUserId($twitchUserId)
    {
        return self::where('twitch_userid', $twitchUserId)->first();
    }
}
