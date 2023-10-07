<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Streamer;

class TwitchAPI
{
    public $twitch_access_token = '';
    public $twitch_current_botID = 0;
    public $twitch_current_clientID = '';
    public $twitch_current_secret = '';

    public function getToken($code, $clientID, $secretID, $url = '/login.php')
    {
        try {
            $response = Http::asForm()->post("https://id.twitch.tv/oauth2/token", [
                "grant_type" => "authorization_code",
                "client_id" => $clientID,
                "client_secret" => $secretID,
                "redirect_uri" => env('APP_URL') . $url,
                "code" => $code,
            ]);

            $token = (!empty($response->body()) ? $response->json() : '');

            if (!empty($token['access_token']) || !empty($token['refresh_token'])) {
                $token['m_status'] = 1;
            } else {
                $token['m_status'] = 0;
            }

            return $token;
        } catch (\Exception $e) {
            $token['m_status'] = 0;
            return $token;
        }
    }

    public function validateUser($code)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $code,
            ])->get("https://id.twitch.tv/oauth2/validate");

            $resp = (!empty($response->body()) ? $response->json() : '');
            return $resp;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function RefreshToken($code, $ClientID, $SecretID)
    {
        try {
            $response = Http::asForm()->post("https://id.twitch.tv/oauth2/token", [
                "grant_type" => "refresh_token",
                "client_id" => $ClientID,
                "client_secret" => $SecretID,
                "refresh_token" => $code,
            ]);

            $token = (!empty($response->body()) ? $response->json() : '');
            if (!empty($token['access_token']) || !empty($token['refresh_token'])) {
                $token['m_status'] = 1;
            } else {
                $token['m_status'] = 0;
            }

            return $token;
        } catch (\Exception $e) {
            $token['m_status'] = 0;
            return $token;
        }
    }

    public function getUserTokenByUserID($twitchUserID)
    {
        if (empty($twitchUserID)) {
            return false;
        }

        $result = DB::table('users')
            ->select('twitch_a', 'twitch_r', 'twitch_expire')
            ->where('twitch_userid', $twitchUserID)
            ->get()
            ->toArray();

        if (empty($result[0]->twitch_a) || empty($result[0]->twitch_r)) {
            return false;
        }

        $validate_token = $this->validateUser($result[0]->twitch_a);
        if (!empty($validate_token['login'])) {
            return $result[0]->twitch_a;
        }

        $RefreshToken_response = $this->RefreshToken($result[0]->twitch_r, MAIN_TWITCH_BOT_TOKEN['client_id'], MAIN_TWITCH_BOT_TOKEN['client_secret']);
        if (empty($RefreshToken_response['access_token']) || empty($RefreshToken_response['expires_in'])) {
            DB::table('users')
                ->where('twitch_userid', $twitchUserID)
                ->update(['twitch_a' => '', 'twitch_r' => '']);
            return false;
        }

        $user_data_array['twitch_a'] = $RefreshToken_response['access_token'];
        $user_data_array['twitch_expire'] = date("Y-m-d H:i:s", time() + $RefreshToken_response['expires_in'] - 100);

        DB::table('users')
            ->where('twitch_userid', $twitchUserID)
            ->update($user_data_array);

        return $user_data_array['twitch_a'];
    }

    public function getAppTokenByUserID($twitchUserID)
    {
        if (empty($twitchUserID)) {
            return false;
        }

        $result = DB::table('users')
            ->select('twitch_a', 'twitch_r', 'twitch_expire')
            ->where('twitch_userid', $twitchUserID)
            ->get()
            ->toArray();

        if (empty($result[0]->twitch_a) || empty($result[0]->twitch_r)) {
            return false;
        }

        $validate_token = $this->validateUser($result[0]->twitch_a);
        if (!empty($validate_token['login'])) {
            return $result[0]->twitch_a;
        }

        $RefreshToken_response = $this->RefreshToken($result[0]->twitch_r, MAIN_TWITCH_BOT_TOKEN['client_id'], MAIN_TWITCH_BOT_TOKEN['client_secret']);
        if (empty($RefreshToken_response['access_token']) || empty($RefreshToken_response['expires_in'])) {
            DB::table('users')
                ->where('twitch_userid', $twitchUserID)
                ->update(['twitch_a' => '', 'twitch_r' => '']);
            return false;
        }

        $user_data_array['twitch_a'] = $RefreshToken_response['access_token'];
        $user_data_array['twitch_expire'] = date("Y-m-d H:i:s", time() + $RefreshToken_response['expires_in'] - 100);

        DB::table('users')
            ->where('twitch_userid', $twitchUserID)
            ->update($user_data_array);

        return $user_data_array['twitch_a'];
    }

    public function disconnect($code, $ClientID)
    {
        try {
            Http::asForm()->post("https://id.twitch.tv/oauth2/revoke", [
                "client_id" => $ClientID,
                "token" => $code,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function Bot_Get_Twitch_Token($random = false, $custom_tokenID = false)
    {
        if (empty($random) && empty($custom_tokenID) && !empty($this->twitch_access_token)) {
            return $this->twitch_access_token;
        }

        $bot_id = rand(0, count(TWITCH_BOTS_TOKEN) - 1);
        $this->twitch_current_botID = $bot_id;
        if (!empty($custom_tokenID)) {
            $this->twitch_current_botID = $custom_tokenID;
        }

        $this->twitch_current_clientID = TWITCH_BOTS_TOKEN[$bot_id]['client_id'];
        $this->twitch_current_secret = TWITCH_BOTS_TOKEN[$bot_id]['client_secret'];

        $result = DB::table('twitch_settings')->select('tw_token', 'tw_expire')->where('id', $bot_id)->get();
        if (!empty($result[0]->tw_token) && !empty($result[0]->tw_expire) && strtotime($result[0]->tw_expire) > time()) {
            $this->twitch_access_token = $result[0]->tw_token;
            return $result[0]->tw_token;
        }

        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'client_id' => $this->twitch_current_clientID,
            'client_secret' => $this->twitch_current_secret,
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();
                if (empty($resp_arr['access_token'])) {
                    return false;
                }

                if (DB::table('twitch_settings')->where('id', $bot_id)->exists()) {
                    DB::table('twitch_settings')->where('id', $bot_id)->update([
                        'tw_token' => $resp_arr['access_token'],
                        'tw_expire' => date('Y-m-d H:i:s', $resp_arr['expires_in'] + time() - 60)
                    ]);
                } else {
                    DB::table('twitch_settings')->insert([
                        'id' => $bot_id,
                        'tw_token' => $resp_arr['access_token'],
                        'tw_expire' => date('Y-m-d H:i:s', $resp_arr['expires_in'] + time() - 60)
                    ]);
                }

                $this->twitch_access_token = $resp_arr['access_token'];
                return $resp_arr['access_token'];
            } catch (\Exception $e) { }
        }

        return false;
    }

    public function Bot_Login_User($username, $database_userid = false) {
        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if(empty($bot_token)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get('https://api.twitch.tv/helix/users', [
            'login' => rawurlencode($username)
        ]);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();
                if (empty($resp_arr['data'][0]['id']) && !empty($database_userid)) {
                    DB::table('streamers')
                    ->where('id', $database_userid)
                    ->update(['twitch_data_update' => date("Y-m-d H:i:s", time() - rand(1, 600))]);
                    return false;
                }

                if (!empty($database_userid)) {
                    $datetime = new \DateTime($resp_arr['data'][0]['created_at']);
                    $twitch_account_create = $datetime->format('Y-m-d H:i:s');
                    DB::table('streamers')
                    ->where('id', $database_userid)
                    ->update([
                        'twitch_display_name' => $resp_arr['data'][0]['display_name'],
                        'twitch_description' => $resp_arr['data'][0]['description'],
                        'twitch_profile_image_url' => $resp_arr['data'][0]['profile_image_url'],
                        'twitch_offline_image_url' => $resp_arr['data'][0]['offline_image_url'],
                        'twitch_account_create' => $twitch_account_create,
                        'twitch_account_type' => $resp_arr['data'][0]['broadcaster_type'],
                        'twitch_userid' => $resp_arr['data'][0]['id'],
                        'twitch_data_update' => date("Y-m-d H:i:s", time() - rand(1, 600))
                    ]);
                }

                return $resp_arr['data'][0];
            } catch (\Exception $e) { 
                
            }
        }

        return false;
    }

    public function Bot_Get_Channel_Information($userid, $database_userid = false) {
        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if(empty($bot_token)) {
            return false;
        }
        if (is_array($userid)) {
            $url_end = '';
            foreach($userid as $user) {
                $url_end .= '&broadcaster_id=' . $user;
            }
            $url = 'https://api.twitch.tv/helix/channels?' . trim($url_end, '&');
        } else {
            $url = 'https://api.twitch.tv/helix/channels?broadcaster_id=' . $userid;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get($url);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();

                if (empty($resp_arr['data'][0]['broadcaster_id'])) {
                    return false;
                }

                if (!empty($database_userid)) {
                    foreach ($resp_arr['data'] as $a_user_data) {
                        Streamer::where('twitch_userid', $a_user_data['broadcaster_id'])
                        ->update([
                            'twitch_category_id' => $a_user_data['game_id'],
                            'username' => strtolower($a_user_data['broadcaster_login']),
                            'twitch_display_name' => strtolower($a_user_data['broadcaster_name']),
                            'twitch_title' => $a_user_data['title'],
                            'twitch_data_update' => date("Y-m-d H:i:s", time() - rand(1, 600))
                        ]);
                    }
                }

                return $resp_arr['data'];
            } catch (\Exception $e) { }
        }

        return false;
    }

    public function Get_And_Save_Games($gamelist) {
        if (!is_array($gamelist)) {
            return false;
        }

        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if (empty($bot_token)) {
            return false;
        }

        $url_end = '';
        foreach ($gamelist as $game) {
            $url_end .= '&id=' . $game;
        }

        $url = 'https://api.twitch.tv/helix/games?' . trim($url_end, '&');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get($url);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();

                if (empty($resp_arr['data'][0]['id'])) {
                    return false;
                }

                foreach ($resp_arr['data'] as $a_game_data) {
                    if (!empty($a_game_data['id']) && !DB::table('twitch_games')->where('game_id', $a_game_data['id'])->exists()) {
                        DB::table('twitch_games')->insert([
                            'game_id' => $a_game_data['id'],
                            'title' => $a_game_data['name'],
                            'box_art_url' => $a_game_data['box_art_url'],
                        ]);
                    }
                }
            } catch (\Exception $e) { }
        }
    }

    public function Get_Streams($userIDs) {
        if (!is_array($userIDs)) {
            return false;
        }

        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if (empty($bot_token)) {
            return false;
        }

        $url_end = '';
        foreach ($userIDs as $user) {
            if (!empty($user)) {
                $url_end .= '&user_id=' . $user;
            }
        }

        $url = 'https://api.twitch.tv/helix/streams?' . trim($url_end, '&');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get($url);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();

                if (empty($resp_arr['data'][0]['id'])) {
                    // $database->update('streamers', ['isLive' => 0, 'twitch_viewers' => 0, 'stream_viewers' => 0, 'quality_url' => '', 'master_playlist' => '', 'playlist_cache' => '', 'twitch_thumbnail_url' => '', 'twitch_check_date' => date("Y-m-d H:i:s")], ['twitch_userid' => $userIDs]);
                    return false;
                }

                $all_games = [];
                $users_updated = [];
                foreach ($resp_arr['data'] as $a_user_data) {
                    if (empty($a_user_data['type']) || $a_user_data['type'] != 'live') {
                        continue;
                    }

                    $users_updated[] = $a_user_data['user_id'];
                    if (!empty($a_user_data['game_id']) && !DB::table('twitch_games')->where('game_id', $a_user_data['game_id'])->exists()) {
                        $all_games[$a_user_data['game_id']] = $a_user_data['game_id'];
                    }

                    DB::table('streamers')
                    ->where('twitch_userid', $a_user_data['user_id'])
                    ->update([
                        'isLive' => '1',
                        'twitch_viewers' => $a_user_data['viewer_count'],
                        'twitch_title' => $a_user_data['title'],
                        'stream_start' => date('Y-m-d H:i:s', strtotime($a_user_data['started_at'])),
                        'twitch_category_id' => $a_user_data['game_id'],
                        'twitch_thumbnail_url' => $a_user_data['thumbnail_url'],
                        'twitch_check_date' => date("Y-m-d H:i:s")
                    ]);
                }

                // $userIDs_offline = array_diff($userIDs, $users_updated);
                // $database->update('streamers', ['isLive' => 0, 'twitch_viewers' => 0, 'stream_viewers' => 0, 'quality_url' => '', 'master_playlist' => '', 'playlist_cache' => '', 'twitch_thumbnail_url' => '', 'twitch_check_date' => date("Y-m-d H:i:s")], ['twitch_userid' => $userIDs_offline]);
                DB::table('streamers')
                ->whereIn('twitch_userid', $userIDs)
                ->update(['twitch_check_date' => date("Y-m-d H:i:s")]);
                if (!empty($all_games)) {
                    $this->Get_And_Save_Games($all_games);
                }
            } catch (\Exception $e) { }
        }
    }

    public function Get_Stream($userID) {
        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if (empty($bot_token)) {
            return false;
        }

        $url = 'https://api.twitch.tv/helix/streams?user_id=' . $userID;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get($url);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();
                if (empty($resp_arr['data'][0]['id'])) {
                    return false;
                }

                return $resp_arr['data'][0];
            } catch (\Exception $e) { }
        }

        return false;
    }

    public function Bot_Get_Game($gameid) {
        $result = DB::table('twitch_games')->where('game_id', $gameid)->first();
        if (!empty($result->game_id)) {
            return (array) $result;
        }

        $bot_token = $this->Bot_Get_Twitch_Token(true);
        if (empty($bot_token)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID
        ])->get('https://api.twitch.tv/helix/games?id=' . $gameid);

        if ($response->successful()) {
            try {
                $resp_arr = $response->json();
                if (empty($resp_arr['data'][0]['id'])) {
                    return false;
                }

                DB::table('twitch_games')->insert([
                    'game_id' => $resp_arr['data'][0]['id'],
                    'title' => $resp_arr['data'][0]['name'],
                    'box_art_url' => $resp_arr['data'][0]['box_art_url']
                ]);

                $resp_arr['data'][0]['game_id'] = $resp_arr['data'][0]['id'];
                return (array) $resp_arr['data'][0];
            } catch (\Exception $e) { }
        }

        return false;
    }

    public function Bot_subscribe($userid, $scope) {
        $bot_token = $this->Bot_Get_Twitch_Token();
        if (empty($bot_token)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot_token,
            'Client-Id' => $this->twitch_current_clientID,
            'Content-Type' => 'application/json'
        ])->post('https://api.twitch.tv/helix/eventsub/subscriptions', [
            'type' => $scope,
            'version' => '1',
            'condition' => [
                'user_id' => $userid,
                'broadcaster_user_id' => $userid
            ],
            'transport' => [
                'method' => 'webhook',
                'callback' => site_url_webhook . 'twitch_webhook_callback.php',
                'secret' => TWITCH_SUBEVENT_SECRET
            ]
        ]);

        return $response->successful();
    }

    public function Bot_subscribe_all($userid) {
        $bot_token = $this->Bot_Get_Twitch_Token(true);
        $this->Bot_subscribe($userid, 'stream.online');
        $this->Bot_subscribe($userid, 'stream.offline');
        $this->Bot_subscribe($userid, 'channel.update');
        DB::table('streamers')
            ->where('twitch_userid', $userid)
            ->update(['subscribe_botid' => $this->twitch_current_botID, 'subscribed' => 1]);
        return true;
    }

    public function GetAccessTokenForStreamer($twitchID, $streamer)
    {
        $result = DB::table('users')
            ->select(['id', 'twitch_username', 'twitch_userid', 'user_token', 'token_date'])
            ->where('twitch_userid', $twitchID)
            ->first();

        if (empty($result->id) || empty($streamer)) {
            return false;
        }

        $token_access_token = $this->getUserTokenByUserID($twitchID);
        if (empty($token_access_token)) {
            return false;
        }

        $output = [];
        if (!empty($result->user_token) && !empty($result->token_date) && strtotime($result->token_date) > time() + 86400) {
            $output['user_token'] = $result->user_token;
            $output['token_date'] = $result->token_date;
            $output['username'] = strtolower(strip_tags($result->twitch_username));
            $output['streamer_token'] = md5(strtolower($streamer) . '_' . $output['user_token']);
            $output['streamer'] = strtolower($streamer);
        } else {
            $output['user_token'] = md5(time() . '_' . $result->id . '_' . $result->twitch_username  . '_' . time() . rand(1,999999));
            $output['token_date'] = date("Y-m-d H:i:s", time() + 172800);
            DB::table('users')
                ->where('twitch_userid', $twitchID)
                ->update($output);
            $output['username'] = strtolower(strip_tags($result->twitch_username));
            $output['streamer_token'] = md5(strtolower($streamer) . '_' . $output['user_token']);
            $output['streamer'] = strtolower($streamer);
        }

        return $output;
    }

}
