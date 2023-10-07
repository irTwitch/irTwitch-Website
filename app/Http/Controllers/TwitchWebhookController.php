<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Streamer;
use App\Helpers\FirebaseAPI;

class TwitchWebhookController extends Controller
{
    public function callback(Request $request)
    {

        $input = $request->getContent();
        if (empty($input)) {
            return response()->json(['error' => 'Something is missing 1'], 400);
        }

        if (empty($request->header('twitch-eventsub-message-type')) && empty($request->header('HTTP_TWITCH_EVENTSUB_MESSAGE_TYPE'))) {
            return response()->json(['error' => 'Something is missing 2'], 400);
        }

        $twitchEventType  = (!empty($request->header('HTTP_TWITCH_EVENTSUB_MESSAGE_TYPE'))) ? $request->header('HTTP_TWITCH_EVENTSUB_MESSAGE_TYPE') : $request->header('twitch-eventsub-message-type');
        $post_array = json_decode($input, true);

        if ($twitchEventType == 'webhook_callback_verification') {
            $post_array = json_decode($input, true);
            if (!empty($post_array['challenge']) && !empty($post_array['subscription']['status']) && !empty($post_array['subscription']['id']) && $post_array['subscription']['status'] == 'webhook_callback_verification_pending') {

                if (!empty($post_array['subscription']['condition']['user_id'])) {
                    Streamer::where('twitch_userid', $post_array['subscription']['condition']['user_id'])->update(['subscribed' => 1]);
                } elseif (!empty($post_array['subscription']['condition']['broadcaster_user_id'])) {
                    Streamer::where('twitch_userid', $post_array['subscription']['condition']['broadcaster_user_id'])->update(['subscribed' => 1]);
                }

                return response($post_array['challenge'], 200)->header('Content-Type', 'text/plain');
            }
        }

        if ($twitchEventType == 'notification') {
            $post_array = json_decode($input, true);
            if (empty($post_array['subscription']['type'])) {
                return;
            }

            if ($post_array['subscription']['type'] == 'stream.online' && !empty($post_array['subscription']['condition']['broadcaster_user_id']) && !empty($post_array['event']['started_at'])) {
                $twitch_userid = $post_array['subscription']['condition']['broadcaster_user_id'];
                Streamer::where('twitch_userid', $twitch_userid)->update([
                    'isLive' => 1,
                    'twitch_viewers' => 0,
                    'stream_viewers' => 0,
                    'stream_start' => date("Y-m-d H:i:s", strtotime($post_array['event']['started_at'])),
                    'quality_url' => '',
                    'master_playlist' => '',
                    'master_playlist_original' => '',
                    'playlist_cache' => ''
                ]);
                
                $FirebaseAPI = new FirebaseAPI();
                $FirebaseAPI->sendLiveNotification($twitch_userid);
            } elseif ($post_array['subscription']['type'] == 'stream.offline' && !empty($post_array['subscription']['condition']['broadcaster_user_id'])) {
                Streamer::where('twitch_userid', $post_array['subscription']['condition']['broadcaster_user_id'])->update([
                    'isLive' => 0,
                    'twitch_viewers' => 0,
                    'stream_viewers' => 0,
                    'quality_url' => '',
                    'master_playlist' => '',
                    'master_playlist_original' => '',
                    'playlist_cache' => ''
                ]);
            } elseif ($post_array['subscription']['type'] == 'channel.update' && !empty($post_array['subscription']['condition']['broadcaster_user_id']) && !empty($post_array['event']['title'])) {
                $update = [
                    'twitch_category_id' => $post_array['event']['category_id'],
                    'twitch_title' => $post_array['event']['title'],
                ];

                Streamer::where('twitch_userid', $post_array['subscription']['condition']['broadcaster_user_id'])->update($update);
            }
        }
    }
}
