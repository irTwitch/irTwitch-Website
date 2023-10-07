<?php

namespace App\Http\Controllers;
use App\Models\Streamer;
use App\Helpers\TwitchAPI;

class StreamViewsController extends Controller
{
    public function __construct()
    {
        $currentDomain = request()->getHttpHost();
        $desiredDomain = str_replace('https://', '', env('APP_URL'));

        if ($currentDomain !== $desiredDomain) {
            header('location: ' . env('APP_URL'));
            exit();
        }
    }

    public function viewStream($username)
    {
        $streamer = Streamer::where('username', $username)
            ->where('isBan', 0)
            ->where('is_relay', 1)
            ->first();

        if ($streamer) {
            $twitchAPI = new TwitchAPI();
            $page_title = (!empty($streamer->twitch_display_name) ? 'irTwitch - ' . GetCleanInput($streamer->twitch_display_name) : 'irTwitch.iR Streaming Service');
            $username = strtolower(GetCleanInput($streamer->username));

            if(!empty($streamer->twitch_category_id))
            {
                $game_data = $twitchAPI->Bot_Get_Game($streamer->twitch_category_id);
            } else {
                $game_data = array();
            }

            $twitch_views = $streamer->twitch_viewers ?? 0;
            $stream_viewers = $streamer->stream_viewers ?? 0;
            $total_views = $twitch_views + $stream_viewers;
            $stram_start = (!empty($streamer->stream_start) && $streamer->isLive == 1) ? time() - strtotime($streamer->stream_start) : 0;
            
            if (!empty($_SESSION['twitch_userid'])) {
                $user_chat_data = $twitchAPI->GetAccessTokenForStreamer($_SESSION['twitch_userid'], $username);
            } else {
                $user_chat_data = array();
                return abort(503, ':(');
            }

            $_SESSION['imRead'] = true;

            return view('streamer_view', [
                'streamer' => $streamer,
                'page_title' => $page_title,
                'user_chat_data' => $user_chat_data,
                'stream_viewers' => $stream_viewers,
                'twitch_views' => $twitch_views,
                'game_data' => $game_data,
                'total_views' => $total_views,
                'stram_start' => $stram_start,
            ]);
        } else {
            // return redirect()->to(env('APP_URL'));
            return abort(404, 'Page not found');
        }
    }
}
