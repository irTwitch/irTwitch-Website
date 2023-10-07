<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Streamer;
use App\Models\StreamViewers;
use App\Models\TwitchGame;
use App\Helpers\TwitchAPI;
use App\Models\FilterCheck;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    protected $twitchApi;

    public function __construct()
    {
        $this->twitchApi = new TwitchAPI();
    }

    public function free_api(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $streamer = $request->input('streamer');
        $all = $request->input('all');
        $appVersion = $request->input('app_version');

        if (empty($streamer) || is_array($streamer) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $streamer)) {
            if (!isset($all)) {
                return response()->json(['error' => 1, 'message' => 'Streamer is not valid.']);
            } else {
                $loadType = 'all';
            }
        } else {
            $loadType = 'streamer';
        }

        if ($loadType === 'streamer') {
            $streamer = strtolower($streamer);

            $cachedData = Cache::get('streamer_' . $streamer);
            if (!is_null($cachedData)) {
                $result = $cachedData;
            } else {
                $result = Streamer::select('twitch_userid', 'username', 'twitch_profile_image_url', 'isLive', 'twitch_title', 'stream_start', 'twitch_category_id', 'twitch_viewers', 'stream_viewers')
                ->where('username', '=', GetCleanInput(strtolower($streamer)))
                ->get();

                Cache::put('streamer_' . $streamer, $result, 30);
            }
            

            if ($result->isEmpty()) {
                return response()->json(['error' => 1, 'message' => 'Streamer does not exist in our database.']);
            }

            $result = $result[0];
            $data = [
                'error' => intval('0'),
                'data' => [
                    'twitch_id' => intval($result->twitch_userid),
                    'is_live' => (!empty($result->isLive)),
                    'username' => (!empty($result->username) ? $result->username : ''),
                    'avatar' => (!empty($result->twitch_profile_image_url) ? str_replace('https://static-cdn.jtvnw.net/', env('APP_URL') . '/images/', str_replace('300x300', '70x70', str_replace('{width}x{height}', '70x70', $result->twitch_profile_image_url))) : ''),
                    'title' => $result->twitch_title,
                    'start_time' => $result->stream_start,
                    'game_title' => '',
                    'twitch_viewers' => intval($result->twitch_viewers),
                    'irtwitch_viewers' => intval($result->stream_viewers),
                    'total_views' => intval($result->twitch_viewers + $result->stream_viewers),
                ],
            ];

            if (!empty($result->twitch_category_id)) {
                $game_response = $this->twitchApi->Bot_Get_Game($result->twitch_category_id);
            }

            if(!empty($game_response['title']))
            {
                $data['data']['game_title'] = $game_response['title'];
            }

            if ($data['data']['is_live'] === false) {
                $data['data']['twitch_viewers'] = 0;
                $data['data']['irtwitch_viewers'] = 0;
            }

            $ip = GetUserIP();
            if (!empty($ip)) {
                StreamViewers::where([
                    'streamer' => $streamer,
                    'user_ip' => $ip,
                ])->update(['attempt' => 2, 'check_date' => date("Y-m-d H:i:s")]);
            }

            return response()->json($data);
        } elseif ($loadType === 'all') {
            if (empty($appVersion)) {
                return response()->json(['error' => 'Application not supported!']);
            }

            if (version_compare($appVersion, min_app_version, '<')) {
                return response()->json(['error_code' => 99, 'error' => 'Upgrade Required!']);
            }

            $cachedData = Cache::get('freeapi_allstreamers');
            if (!is_null($cachedData)) {
                $data = $cachedData;
            } else {
                $result = DB::select("
                    SELECT username, twitch_userid, is_relay, twitch_display_name, twitch_category_id, twitch_profile_image_url, twitch_title, twitch_thumbnail_url, twitch_viewers, stream_viewers, (twitch_viewers + stream_viewers) as total_views
                    FROM streamers
                    WHERE isLive = 1 AND isBan = 0 AND twitch_title IS NOT NULL
                    ORDER BY stream_viewers DESC, twitch_viewers DESC
                ");
                $result = Streamer::hydrate($result);

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

                $streamers = [];
                $allViews = 0;
                $allViewsIR = 0;
                $liveStreamers = 0;

                foreach ($result as $stream) {
                    $liveStreamers++;
                    if (!empty($stream->total_views)) {
                        $allViews += $stream->total_views;
                    }
                    if (!empty($stream->stream_viewers)) {
                        $allViewsIR += $stream->stream_viewers;
                    }

                    $arrayTmp = [
                        'twitch_userid' => (int) $stream->twitch_userid,
                        'twitch_username' => $stream->username,
                        'twitch_displayname' => $stream->twitch_display_name,
                        'twitch_avatar' => str_replace('https://static-cdn.jtvnw.net/', app_domain . '/images/', str_replace('300x300', '70x70', str_replace('{width}x{height}', '70x70', $stream->twitch_profile_image_url))),
                        'twitch_title' => $stream->twitch_title,
                        'twitch_description' => $stream->twitch_description,
                        'twitch_video_thumbnail' => str_replace('https://static-cdn.jtvnw.net/', app_domain . '/images/', str_replace('{width}x{height}', '1280x720', $stream->twitch_thumbnail_url)),
                        'twitch_viewers' => (int) $stream->twitch_viewers,
                        'irtw_viewers' => (int) $stream->stream_viewers,
                        'game_title' => '',
                        'game_id' => '',
                        'game_image' => '',
                    ];

                    if (!empty($stream->twitch_category_id)) {
                        $gameData = TwitchGame::where('game_id', $stream->twitch_category_id)->first();

                        if (!empty($gameData)) {
                            $arrayTmp['game_title'] = $gameData->title;
                            $arrayTmp['game_id'] = $gameData->game_id;
                            $arrayTmp['game_image'] = $gameData->box_art_url;
                        }
                    }

                    $streamers[] = $arrayTmp;
                }

                $data = [
                    // 'site' => env("APP_URL") . '/',
                    'site' => app_domain,
                    'all_viewers' => $allViews,
                    'irtw_viewers' => $allViewsIR,
                    'live_streamers' => $liveStreamers,
                    'streamers' => $streamers,
                ];
                Cache::put('freeapi_allstreamers', $data, 30);
            }

            return response()->json($data);
        }
    }

    public function FilteringCheck(Request $request){
        if (!empty($request->input('d'))) {
            return response()->json([
                'domain' => parse_url(env('APP_URL'), PHP_URL_HOST),
                'f_domains' => filtered_domains
            ]);
        }

        if (!empty($request->input('g'))) {
            $result = FilterCheck::select('id', 'isLatestDomainOk', 'isLatestDomainAccessible', 'date_update')
                ->where('id', 1)
                ->first();

            if (empty($result)) {
                return response()->json(['status' => 0]);
            }

            if ($result->isLatestDomainOk == 'Yes') {
                $result->isLatestDomainOk = '<a:greenCircle:1061786019160145991> Site Filter Nist <a:greenCircle:1061786019160145991>';
            } elseif ($result->isLatestDomainOk == 'No') {
                $result->isLatestDomainOk = '<a:redCircle:1061786022238748762> Site Filter Shode! <a:redCircle:1061786022238748762>';
            } else {
                $result->isLatestDomainOk = '<a:loading:1061786026311417906> ?! <a:loading:1061786026311417906>';
            }

            if ($result->isLatestDomainAccessible == 'Yes') {
                $result->isLatestDomainAccessible = '<a:greenCircle:1061786019160145991> Site az Iran baz mishe! <a:greenCircle:1061786019160145991>';
            } else {
                $result->isLatestDomainAccessible = '<a:loading:1061786026311417906> ?! <a:loading:1061786026311417906>';
            }

            return response()->json([
                'domain' => parse_url(env('APP_URL'), PHP_URL_HOST),
                'lastDomain' => filtered_domains[count(filtered_domains) - 1],
                'isLatestDomainOk' => $result->isLatestDomainOk,
                'isLatestDomainAccessible' => $result->isLatestDomainAccessible,
                'date_update' => jdate("Y/m/d H:i", strtotime($result->date_update))
            ]);
        }

        if (!empty($request->input('u'))) {
            $post = json_decode($request->getContent(), true);

            if (empty($post['isLatestDomainAccessible']) || empty($post['isLatestDomainOk'])) {
                return response()->json(['status' => 0]);
            } else {
                try {
                    FilterCheck::where('id', 1)
                        ->update([
                            'isLatestDomainOk' => $post['isLatestDomainOk'],
                            'isLatestDomainAccessible' => $post['isLatestDomainAccessible'],
                            'date_update' => date('Y-m-d H:i:s')
                        ]);

                    return response()->json(['status' => 1]);
                } catch (\Exception $e) {
                    return response()->json(['status' => 0]);
                }
            }
        }

        return response()->json(['status' => 1]);
    }

    public function handleDiscordBotRequest(Request $request)
    {
        $auth_API_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $input = $request->getContent();

        if (empty($input)) {
            return response()->json(['error' => 1, 'message' => 'Unauthenticated request!']);
        }

        try {
            $json_input = json_decode($input, true);
        } catch (\Exception $e) {
            return response()->json(['error' => 1, 'message' => 'Unauthenticated request!']);
        }

        if (empty($json_input["auth_key"]) || $json_input["auth_key"] != $auth_API_key) {
            return response()->json(['error' => 1, 'message' => 'Unauthenticated request!']);
        }

        if (empty($json_input['method'])) {
            return response()->json(['error' => 1, 'message' => 'Method required.']);
        }

        if (!empty($json_input['method'] == 'new_streamer')) {
            return $this->new_streamer($json_input);
        } elseif ($json_input['method'] == 'live_streams') {
            return $this->live_streams();
        } elseif ($json_input['method'] == 'get_links') {
            return $this->get_links($json_input);
        } elseif ($json_input['method'] == 'update_links') {
            return $this->update_links($json_input);
        } elseif (!empty($json_input['method'] == 'verfiy_streamer_to_play')) {
            return $this->verify_streamer_to_play($json_input);
        } elseif (!empty($json_input['method'] == 'add_streamer_to_play')) {
            return $this->add_streamer_to_play($json_input);
        } else {
            return response()->json(['error' => 1, 'message' => 'Invalid parameters!']);
        }
    }

    private function new_streamer($json_input)
    {
        if (empty($json_input['username'])) {
            return response()->json(['error' => 1, 'message' => 'Username is required.']);
        }

        $username = strtolower($json_input['username']);
        if (is_array($username) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $username)) {
            return response()->json(['error' => 1, 'message' => 'Name Karbari Streamer be dorosti vared nashode.']);
        }

        $username = getCleanInput(strtolower($username));
        $result = Streamer::where('username', $username)->first();
        if (!empty($result)) {
            return response()->json(['error' => 1, 'message' => 'In streamer ghablan sabt shode.']);
        }

        $user_login = $this->twitchApi->Bot_Login_User($username);
        if (empty($user_login) || empty($user_login['id'])) {
            return response()->json(['error' => 1, 'message' => 'In account Twitch mojod nist.']);
        }

        $stream_status = $this->twitchApi->Get_Stream($user_login['id']);
        if (!empty($json_input['owner'])) {
            if (!empty($stream_status['id'])) {
                Streamer::create(['username' => $username, 'isLive' => 1]);
                return response()->json(['error' => 0, 'message' => 'Streamer successfully added to the site.']);
            } else {
                Streamer::create(['username' => $username, 'isLive' => 0]);
                return response()->json(['error' => 0, 'message' => 'Streamer successfully added to the site.']);
            }
        }

        if (empty($stream_status['id']) || empty($stream_status['type']) || empty($stream_status['title']) || $stream_status['type'] != 'live') {
            return response()->json(['error' => 1, 'message' => 'Baraye Ezafe kardane Streamer, Streamer bayad live bashe va in Streamer Alan Live nis!']);
        }

        $stream_title = $stream_status['title'];
        if(!(strpos(strtolower($stream_title), 'iran') !== false || strpos(strtolower($stream_title), 'persian') !== false || strpos(strtolower($stream_title), 'farsi') !== false || strpos(strtolower($stream_title), 'ایران') !== false || strpos(strtolower($stream_title), 'فارسی') !== false || strpos(strtolower($stream_title), '[fa/') !== false || strpos(strtolower($stream_title), '(fa/') !== false || strpos(strtolower($stream_title), '(per/') !== false || strpos(strtolower($stream_title), '[per/') !== false || strpos(strtolower($stream_title), '/per]') !== false|| strpos(strtolower($stream_title), '/per)') !== false|| strpos(strtolower($stream_title), '/fa]') !== false|| strpos(strtolower($stream_title), '/fa)') !== false)) {
            return response()->json(['error' => 1, 'message' => 'Onvane Stream bayad shamele Iran ya Persian ya Farsi bashe.']);
        }

        Streamer::create(['username' => $username, 'isLive' => 1]);
        return response()->json(['error' => 0, 'message' => 'Streamer ba movafaghiat be site ezafe shod.']);

    }

    private function live_streams()
    {
        $totalStreams = Streamer::where('isLive', 1)
            ->where('isBan', 0)
            ->whereNotNull('twitch_title')
            ->count();

        if (!empty($totalStreams)) {
            return response()->json(['streams' => intval($totalStreams)]);
        } else {
            return response()->json(['streams' => 0]);
        }
    }

    private function get_links($json_input)
    {
        // Validate the Discord user ID
        if (empty($json_input['discord_id']) || !is_numeric($json_input['discord_id'])) {
            return response()->json(['error' => 1, 'message' => 'Discord userID is required.']);
        }

        // Find the streamer by Discord user ID using the Streamer model
        $streamer = Streamer::select('username', 'discord_userid', 'isBan', 'is_relay', 'stream_links')
            ->where('discord_userid', GetCleanInput($json_input['discord_id']))
            ->first();

        if (!$streamer) {
            return response()->json(['error' => 1, 'message' => 'In bakhsh faghat makhsoose streamer haii hast ke to site pakhsh mishan, va baraye account discord shoma hich id twitchi sabt nashode, age darkhast pakhsh dadin, sabr konin taeed beshe bad az in ghabeliat mitoni estefade koni.']);
        }

        // Check if the streamer is banned or not a relay
        if ($streamer->isBan || !$streamer->is_relay) {
            return response()->json(['error' => 1, 'message' => 'Emkane Modiriate in streamer darhale hazer vojod nadare.']);
        }

        // Prepare the stream links
        $links_array = [
            'donate' => '',
            'youtube' => '',
            'instagram' => '',
            'discord' => ''
        ];

        if (!empty($streamer->stream_links)) {
            $stream_links = json_decode($streamer->stream_links, true);
            $links_array['donate'] = (!empty($stream_links['donate']) && filter_var($stream_links['donate'], FILTER_VALIDATE_URL)) ? GetCleanInput($stream_links['donate']) : '';
            $links_array['youtube'] = (!empty($stream_links['youtube']) && filter_var($stream_links['youtube'], FILTER_VALIDATE_URL)) ? GetCleanInput($stream_links['youtube']) : '';
            $links_array['instagram'] = (!empty($stream_links['instagram']) && filter_var($stream_links['instagram'], FILTER_VALIDATE_URL)) ? GetCleanInput($stream_links['instagram']) : '';
            $links_array['discord'] = (!empty($stream_links['discord']) && filter_var($stream_links['discord'], FILTER_VALIDATE_URL)) ? GetCleanInput($stream_links['discord']) : '';
        }

        // Prepare the output data
        $output_data = [
            'username' => $streamer->username,
            'discord_id' => $streamer->discord_userid,
            'stream_links' => $links_array,
            'page_link' => env('APP_URL') . '/' . $streamer->username,
            'widget_urls' => [env('APP_URL') . '/' . 'widget.php?streamer=' . $streamer->username]
        ];

        return response()->json(['error' => 0, 'data' => $output_data]);
    }

    private function update_links($json_input)
    {
        if (empty($json_input['discord_id']) || !is_numeric($json_input['discord_id'])) {
            return response()->json(['error' => 1, 'message' => 'Discord userID is required.']);
        }

        // Find the streamer by Discord user ID using the Streamer model
        $streamer = Streamer::where('discord_userid', GetCleanInput($json_input['discord_id']))
        ->select('username', 'discord_userid', 'isBan', 'is_relay', 'stream_links')
        ->first();
        if (!$streamer) {
            return response()->json(['error' => 1, 'message' => 'In bakhsh faghat makhsoose streamer haii hast ke to site pakhsh mishan, va baraye account discord shoma hich id twitchi sabt nashode, age darkhast pakhsh dadin, sabr konin taeed beshe bad az in ghabeliat mitoni estefade koni.']);
        }

        // Check if the streamer is banned or not a relay
        if ($streamer->isBan || !$streamer->is_relay) {
            return response()->json(['error' => 1, 'message' => 'Emkane Modiriate in streamer darhale hazer vojod nadare.']);
        }

        $stream_links = [
            'donate' => !empty($json_input['donate']) && filter_var($json_input['donate'], FILTER_VALIDATE_URL) ? GetCleanInput($json_input['donate']) : '',
            'youtube' => !empty($json_input['youtube']) && filter_var($json_input['youtube'], FILTER_VALIDATE_URL) ? GetCleanInput($json_input['youtube']) : '',
            'instagram' => !empty($json_input['instagram']) && filter_var($json_input['instagram'], FILTER_VALIDATE_URL) ? GetCleanInput($json_input['instagram']) : '',
            'discord' => !empty($json_input['discord']) && filter_var($json_input['discord'], FILTER_VALIDATE_URL) ? GetCleanInput($json_input['discord']) : '',
        ];

        $stream_links_json = json_encode($stream_links);
        Streamer::where('discord_userid', $json_input['discord_id'])->update(['stream_links' => $stream_links_json]);
        return response()->json(['error' => 0, 'message' => 'Link haye stream taghir kard.']);
    }

    private function verify_streamer_to_play($json_input)
    {
        if (empty($json_input['username'])) {
            return response()->json(['error' => 1, 'message' => 'Username is required.']);
        }

        if (empty($json_input['discord_id']) || !is_numeric($json_input['discord_id'])) {
            return response()->json(['error' => 1, 'message' => 'Discord userID is required.']);
        }

        $username = strtolower($json_input['username']);
        if(is_array($username) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $username))
        {
            return response()->json(['error' => 1, 'message' => 'Name Karbari Streamer be dorosti vared nashode.']);
        }

        $result = Streamer::select('id', 'username', 'is_relay')
            ->where('username', $username)
            ->get()
            ->toArray();

        if (empty($result[0]['id'])) {
            return response()->json(['error' => 1, 'message' => 'Baraye dastrasi be in bakhsh, aval streamer ro to site add konin (Dokme samte chap).']);
        }

        if (!empty($result[0]['discord_userid'])) {
            return response()->json(['error' => 1, 'message' => 'In Streamer ghablan ezafe shode!']);
        }

        $result2 = Streamer::select('id', 'username', 'is_relay', 'discord_userid')
            ->where('discord_userid', GetCleanInput($json_input['discord_id']))
            ->get()
            ->toArray();

        if (!empty($result2[0]['username'])) {
            return response()->json(['error' => 1, 'message' => 'Account discord shoma be account Twitch ' . GetCleanInput($result2[0]['username']) . ' motasel shode.']);
        }

        return response()->json(['error' => 0, 'message' => 'Darkhaste shoma taeed shod, Dar soorat taeed ya Rad shodan, Bot be shoma PM mide.']);
    }

    private function add_streamer_to_play($json_input)
    {
        if (empty($json_input['username'])) {
            return response()->json(['error' => 1, 'message' => 'Username is required.']);
        }

        if (empty($json_input['discord_id']) || !is_numeric($json_input['discord_id'])) {
            return response()->json(['error' => 1, 'message' => 'Discord userID is required.']);
        }

        $username = strtolower($json_input['username']);
        if(is_array($username) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $username))
        {
            return response()->json(['error' => 1, 'message' => 'Name Karbari Streamer be dorosti vared nashode.']);
        }

        $result = Streamer::select('id', 'username', 'is_relay')
            ->where('username', $username)
            ->get()
            ->toArray();

        if (empty($result[0]['id'])) {
            return response()->json(['error' => 1, 'message' => 'Aval Streamer ro to site ezafe konin.']);
        }

        if (!empty($result[0]['discord_userid'])) {
            return response()->json(['error' => 1, 'message' => 'In Streamer be account discord dgaii motasel hast']);
        }

        $result2 = Streamer::select('id', 'username', 'is_relay', 'discord_userid')
            ->where('discord_userid', GetCleanInput($json_input['discord_id']))
            ->get()
            ->toArray();

        if (!empty($result2[0]['username'])) {
            return response()->json(['error' => 1, 'message' => 'In Account discord be account Twitch ' . GetCleanInput($result2[0]['username']) . ' motasel shode.']);
        }

        Streamer::where('username', $username)
            ->update(['is_relay' => 1, 'discord_userid' => $json_input['discord_id']]);

            return response()
            ->json(['error' => 0, 'message' => 'Darkhast ba movafaghiat anjam shod.', 'username' => $username, 'link' => env('APP_URL') . '/' . $username, 'overlay' => env('APP_URL') . '/widget.php?streamer=' . $username])
            ->header('Content-Type', 'application/json; charset=utf-8');
    }
}
