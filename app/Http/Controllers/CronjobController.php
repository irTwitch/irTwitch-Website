<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\TwitchToken;
use App\Models\Streamer;
use App\Models\StreamViewers;
use App\Models\StreamTsCache;
use App\Models\TwitchGame;
use App\Helpers\TwitchAPI;

class CronjobController extends Controller
{
    public function KasadaToken() {
        $url = 'http://xxxxxxxxxxxxxxxxxxxx/getToken';
        $response = Http::get($url);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch token'], 500);
        }

        $json_data = $response->json();

        if (empty($json_data['headers']['Client-Integrity'])) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $data = [
            'sec-ch-ua' => $json_data['headers']['sec-ch-ua'],
            'sec-ch-ua-mobile' => $json_data['headers']['sec-ch-ua-mobile'],
            'User-Agent' => $json_data['headers']['User-Agent'],
            'Client-Version' => $json_data['headers']['Client-Version'],
            'sec-ch-ua-platform' => $json_data['headers']['sec-ch-ua-platform'],
            'token' => $json_data['headers']['Client-Integrity'],
            'Client-Session-Id' => $json_data['headers']['Client-Session-Id'],
            'Client-Id' => $json_data['headers']['Client-Id'],
            'X-Device-Id' => $json_data['headers']['X-Device-Id']
        ];
        TwitchToken::where('id', 1)->update($data);

        return response()->json(['message' => 'Token updated successfully']);
    }

    public function MainCronJob() {
        $this->cleanupFiles();
        $this->updateStreamers();
        $this->updateLiveStreams();
    }

    public function cleanupFiles()
    {
        $dir = storage_path('../../irtw_cdn/live_streams/segments/');
        foreach (glob($dir . "*.ts") as $file) {
            try {
                if (time() - filectime($file) > 60) {
                    unlink($file);
                }
            } catch (\Exception $e) {
                // Handle exception if needed
            }
        }

        $this->deleteFilesByAge(storage_path('../../irtw_cdn/images/previews-ttv/'), 310);
        $this->deleteFilesByAge(storage_path('../../irtw_cdn/images/jtv_user_pictures/'), 86400);
    }

    private function deleteFilesByAge($directory, $ageInSeconds)
    {
        foreach (glob($directory . "*.jpg") as $file) {
            try {
                if (time() - filectime($file) > $ageInSeconds) {
                    unlink($file);
                }
            } catch (\Exception $e) {
                // Handle exception if needed
            }
        }

        foreach (glob($directory . "*.jpeg") as $file) {
            try {
                if (time() - filectime($file) > $ageInSeconds) {
                    unlink($file);
                }
            } catch (\Exception $e) {
                // Handle exception if needed
            }
        }

        foreach (glob($directory . "*.png") as $file) {
            try {
                if (time() - filectime($file) > $ageInSeconds) {
                    unlink($file);
                }
            } catch (\Exception $e) {
                // Handle exception if needed
            }
        }
    }

    private function updateStreamers()
    {
        $twitchAPI = new TwitchAPI();

        // Delete outdated stream_ts_cache records
        StreamTsCache::where('create_time', '<', date("Y-m-d H:i:s", time() - 60))->delete();

        // Delete outdated stream_viewers records
        StreamViewers::where('check_date', '<', date("Y-m-d H:i:s", time() - 300))->delete();

        // Get streamers that need updates
        $streamers = Streamer::select('id', 'username', 'twitch_userid')
        ->where(function ($query) {
            $query->where('twitch_userid', null)
                ->orWhere('twitch_data_update', '<', date('Y-m-d H:i:s', time() - 86400));
        })
        ->limit(30)
        ->get();

        if (!$streamers->isEmpty()) {
            $userIDsForUpdate = [];
            $userIDsForUpdateUsername = [];

            foreach ($streamers as $streamer) {
                $response = $twitchAPI->Bot_Login_User($streamer->username, $streamer->id);

                if (!empty($response['id'])) {
                    $userIDsForUpdate[] = $response['id'];
                } elseif (!empty($streamer->twitch_userid)) {
                    $userIDsForUpdate[] = $streamer->twitch_userid;
                    $userIDsForUpdateUsername[] = $streamer->twitch_userid;
                }
            }
            $response2 = $twitchAPI->Bot_Get_Channel_Information($userIDsForUpdate, true);
            if (!empty($response2[0]['broadcaster_id'])) {
                if (!empty($userIDsForUpdateUsername[$response2[0]['broadcaster_id']])) {
                    $response = $twitchAPI->Bot_Login_User(strtolower($response2[0]['broadcaster_login']), $streamer->id);
                }

                $all_games = [];

                foreach ($response2 as $a_data) {
                    if (!empty($a_data['game_id']) && !TwitchGame::where('twitch_games.game_id', $a_data['game_id'])->exists()) {
                        $all_games[$a_data['game_id']] = $a_data['game_id'];
                    }
                }

                if (!empty($all_games)) {
                    $twitchAPI->Get_And_Save_Games($all_games);
                }
            }
        }

        // Subscribe streamers that need it
        $streamersToSubscribe = Streamer::whereNotNull('twitch_userid')
            ->where('subscribed', '0')
            ->take(20)
            ->get();

        foreach ($streamersToSubscribe as $streamer) {
            $twitchAPI->Bot_subscribe_all($streamer->twitch_userid);
        }
    }

    private function updateLiveStreams()
    {
        $maxIterations = 10;

        for ($i = 0; $i < $maxIterations; $i++) {
            $streamers = Streamer::select('id', 'username', 'twitch_userid')
            ->where(function ($query) {
                $query->where('twitch_check_date', '<', date('Y-m-d H:i:s', time() - 45))
                    ->orWhereNull('twitch_check_date');
            })
            ->where('isLive', 1)
            ->orderBy('twitch_check_date', 'ASC')
            ->limit(95)
            ->get();

            if ($streamers->isEmpty()) {
                return;
            }

            $ids = [];
            foreach ($streamers as $streamer) {
                $ids[] = $streamer->twitch_userid;

                $viewers_count = StreamViewers::where([
                    'streamer' => $streamer->username,
                    'attempt' => 2
                ])->count();

                Streamer::where('id', $streamer->id)->update([
                    'stream_viewers' => $viewers_count
                ]);
            }

            $twitchAPI = new TwitchAPI();
            $twitchAPI->Get_Streams($ids);
        }
    }
}
