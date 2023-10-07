<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Cache;
use App\Models\Streamer;
use App\Models\UsersFollows;
use App\Models\User;

class FirebaseAPI
{
    public function sendLiveNotification($streamerID)
    {
        try{
            // $cached = Cache::get('notify_' . $streamerID);
            // if (!is_null($cached)) {
            //     return false;
            // }

            Cache::put('notify_' . $streamerID, 1, 3600);

            $streamer = Streamer::getStreamerByTwitchUserId($streamerID);
            if ($streamer) {
                $StreamerName = (!empty($streamer->twitch_display_name)) ? $streamer->twitch_display_name : $streamer->username;
                $title = $StreamerName . ' is live!';
                $message = 'Your favorite streamer is live now! Tune in to catch all the exciting action.';
                $users = UsersFollows::where('streamerid', $streamerID)
                    ->join('users', 'users_follows.userid', '=', 'users.twitch_userid')
                    ->whereNotNull('users.fcmToken')
                    ->where('users.fcmToken_date', '>=', date('Y-m-d', strtotime('-3 days')))
                    ->take(800)
                    ->get(['users.fcmToken']);

                $fcm_tokens = $users->pluck('fcmToken')->toArray();
                if(!empty($fcm_tokens) && count($fcm_tokens) > 0)
                {
                    $this->sendFCMMessage($fcm_tokens, $title, $message, array("title" => $title, "body" => $message));
                    // $this->sendFCMMessage($fcm_tokens, $title, $message, array("empty" => "empty"));
                }
            } 
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function sendFCMMessage($tokens, $title,$message, $data)
    {
        try{
            $privateKey = "";

            $url = 'https://fcm.googleapis.com/v1/projects/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/messages:send';
            $payload = [
                'message' => [
                    'data' => $data,
                    // 'notification' => [
                    //     'title' => $title,
                    //     'body' => $message,
                    // ],
                    'token' => implode(',', $tokens),
                    'android' => [
                        'ttl' => '3600s', // Set TTL to 1 hour (3600 seconds)
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-expiration' => (string)(time() + 3600), // Set the expiration directly in the headers
                        ],
                    ],
                ],
            ];
            $access_token = $this->generateAccessToken($privateKey);
            if(empty($access_token))
            {
                return false;
            }

            $jsonPayload = json_encode($payload);
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function generateAccessToken(string $privateKey)
    {
        try{
            $cachedToken = Cache::get('fcm_access_token');
            if (!is_null($cachedToken)) {
                return $cachedToken;
            }

            // Replace with your service account email
            $serviceAccountEmail = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

            // Replace with the expiration time (in seconds)
            $expirationTime = 3600;

            // Create the header
            $header = base64_encode(json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]));

            // Create the claim set
            $currentTime = time();
            $claimSet = base64_encode(json_encode([
                'iss' => $serviceAccountEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://www.googleapis.com/oauth2/v4/token',
                'exp' => $currentTime + $expirationTime,
                'iat' => $currentTime,
            ]));

            // Create the signature
            $signature = '';
            openssl_sign("$header.$claimSet", $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $signature = base64_encode($signature);

            // Create the JWT
            $jwt = "$header.$claimSet.$signature";

            // Prepare the request URL
            $url = 'https://www.googleapis.com/oauth2/v4/token';

            // Prepare the request body
            $body = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ];

            // Send the request to get the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Parse the response
            $responseData = json_decode($response, true);

            $accessToken = $responseData['access_token'] ?? null;
            if (!is_null($accessToken)) {
                Cache::put('fcm_access_token', $accessToken, $expirationTime - 60);
            }

            return $accessToken;
        } catch (\Exception $e) {
            return false;
        }
    }
}
