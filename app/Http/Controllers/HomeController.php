<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use App\Models\Streamer;
use App\Helpers\TwitchAPI;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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

    public function index()
    {
        $twitchAPI = new TwitchAPI();
        $page_title = 'irTwitch/irTW/TwFa - توییچ فارسی/ایران توییچ';

        $data = Cache::remember('index_streamers', 30, function () {
            $result = DB::select("
                SELECT username, is_relay, twitch_display_name, twitch_category_id, twitch_profile_image_url, twitch_title, twitch_thumbnail_url, twitch_viewers, stream_viewers, (twitch_viewers + stream_viewers) as total_views
                FROM streamers
                WHERE isLive = 1 AND isBan = 0 AND twitch_title IS NOT NULL
                ORDER BY stream_viewers DESC, twitch_viewers DESC
            ");
            $result = Streamer::hydrate($result);

            $all_views = 0;
            $all_viewsIR = 0;
            $randomStreamers = [];
            $remainingStreamers = [];

            if ($result->count() > 3) {
                $result_rand = $result->slice(3)->shuffle();
                $randomStreamers = $result_rand->slice(0, 3);
                $randomStreamersIds = $randomStreamers->pluck('username')->toArray();

                $remainingStreamers = $result->filter(function ($row) use ($randomStreamersIds) {
                    return !in_array($row['username'], $randomStreamersIds);
                });

                $result = $randomStreamers->concat($remainingStreamers);
            }

            foreach ($result as $stream) {
                if (!empty($stream['total_views'])) {
                    $all_views += $stream['total_views'];
                }
                if (!empty($stream['stream_viewers'])) {
                    $all_viewsIR += $stream['stream_viewers'];
                }
            }

            return [
                'result' => $result,
                'all_views' => $all_views,
                'all_viewsIR' => $all_viewsIR
            ];
        });

        return view('home', [
            'twitch_api' => $twitchAPI,
            'page_title' => $page_title,
            'result' => $data['result'],
            'all_views' => $data['all_views'],
            'all_viewsIR' => $data['all_viewsIR'],
            'twitchAPI' => $twitchAPI,
        ]);
    }
}
