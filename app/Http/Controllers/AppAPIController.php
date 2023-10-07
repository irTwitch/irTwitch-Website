<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TwitchAPI;
use App\Models\User;
use App\Models\UsersFollows;
use App\Models\Streamer;

class AppAPIController extends Controller
{
    protected $twitchApi;
    protected $userToken;
    protected $twitchUserID;
    protected $userData;
    protected $appVersion;

    public function __construct(Request $request)
    {
        header('Content-Type: application/json');
        $authorizationHeader = $request->header('Authorization');
        $twitchUserIDHeader = $request->header('UserID');
        $appVersionHeader = $request->header('AppVersion');
        $clientIdHeader = $request->header('ClientId');

        if (!$authorizationHeader || !$twitchUserIDHeader || !$appVersionHeader || !$clientIdHeader) {
            exit(json_encode(['error' => 'Unauthorized', 'code' => 1001]));
        }
        
        if (version_compare($appVersionHeader, min_app_version, '<')) {
            exit(json_encode(['error' => 'Unsupported app version', 'code' => 1002]));
        }

        $authorizationPattern = '/^Bearer [A-Za-z0-9-_]+$/';
        $twitchUserIDPattern = '/^[A-Za-z0-9_]+$/';

        if (!preg_match($authorizationPattern, $authorizationHeader) || !preg_match($twitchUserIDPattern, $twitchUserIDHeader)) {
            exit(json_encode(['error' => 'Unauthorized', 'code' => 1003]));
        }

        if (!in_array($clientIdHeader, api_valid_clients)) {
            exit(json_encode(['error' => 'Invalid client ID', 'code' => 1004]));
        }
        
        $userToken = str_replace('Bearer ', '', $authorizationHeader);

        $user = User::where('twitch_userid', $twitchUserIDHeader)
                ->where('app_token', $userToken)
                ->first();

        if (!$user) {
            exit(json_encode(['error' => 'Invalid user credentials', 'code' => 1005]));
        }
        
        if(empty($user->twitch_r) || empty($user->twitch_a)) {
            exit(json_encode(['error' => 'Invalid user credentials', 'code' => 1005]));
        }
        
        $this->userData = $user;
        $this->userToken = $userToken;
        $this->twitchUserID = $twitchUserIDHeader;
        $this->twitchApi = new TwitchAPI();
    }

    public function logout()
    {
        $user = $this->userData;
        $requestUserID = $this->twitchUserID;
        if ($requestUserID != $user->twitch_userid) {
            exit(json_encode(['error' => 'Unauthorized', 'code' => 1001]));
        }
    
        $user->app_token = null;
        $user->fcmToken = null;
        $user->save();
        return response()->json(['message' => 'Logout successful', 'code' => 2000]);
    }

    public function updateFCMToken(Request $request) {
        $user = $this->userData;
        $requestUserID = $this->twitchUserID;
        
        if ($requestUserID != $user->twitch_userid) {
            exit(json_encode(['error' => 'Unauthorized', 'code' => 1001]));
        }
        
        $fcmToken = $request->input('fcm_token');
    
        // Validate and process the FCM token
        
        $user->fcmToken = $fcmToken;
        $user->fcmToken_date = date("Y-m-d H:i:s");
        $user->save();
    
        return response()->json(['message' => 'FCM token updated successfully', 'code' => 2000]);
    }

    public function followStreamer(Request $request) {
        $userId = $this->twitchUserID;
        $streamerId = $request->input('streamer_id');

        $streamerExists = Streamer::where('twitch_userid', $streamerId)->exists();

        if (!$streamerExists) {
            return response()->json(['error' => 'Streamer does not exist', 'code' => 1006]);
        }

        $isFollowing = UsersFollows::where('userid', $userId)
            ->where('streamerid', $streamerId)
            ->exists();
    
        if ($isFollowing) {
            UsersFollows::where('userid', $userId)
                ->where('streamerid', $streamerId)
                ->delete();
    
            $result_json = ['message' => 'Unfollow successful', 'code' => 2001];
        } else {
            UsersFollows::create([
                'userid' => $userId,
                'streamerid' => $streamerId,
            ]);
            $result_json = ['message' => 'Follow successful', 'code' => 2000];
        }

        $userId = $this->twitchUserID;
        $follows = UsersFollows::where('userid', $userId)->get();
        $result = [];
        foreach ($follows as $follow) {
            $result[] = $follow->streamerid;
        }
        $result_json['follows'] = $result;
        
        return response()->json($result_json);
    }
    
    public function userFollows() {
        $userId = $this->twitchUserID;
        $follows = UsersFollows::where('userid', $userId)->get();
        $result = [];
        foreach ($follows as $follow) {
            $result[] = $follow->streamerid;
        }

        return response()->json(['follows' => $result, 'code' => 2000]);
    }
}
