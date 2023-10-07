<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\TwitchAPI; // Update with the correct TwitchAPI service namespace

class AuthController extends Controller
{
    public function __construct()
    {

    }

    public function login(Request $request)
    {
        $currentDomain = request()->getHttpHost();
        $desiredDomain = str_replace('https://', '', env('APP_URL'));

        if ($currentDomain !== $desiredDomain) {
            header('location: ' . env('APP_URL') . '/login.php');
            exit();
        }

        if (!empty($_SESSION['twitch_userid'])) {
            return redirect(env('APP_URL'));
        }

        if (!empty($request->input('code'))) {
            $twitch_api = new TwitchAPI();
            $token_data = $twitch_api->GetToken(
                GetCleanInput($request->input('code')),
                MAIN_TWITCH_BOT_TOKEN['client_id'],
                MAIN_TWITCH_BOT_TOKEN['client_secret']
            );

            if (empty($token_data['m_status']) || empty($token_data['access_token'])) {
                return redirect(env('APP_URL'). '/login.php');
            }

            $user_data_array['twitch_r'] = $token_data['refresh_token'];
            $user_data_array['twitch_a'] = $token_data['access_token'];
            $user_data_array['twitch_expire'] = date("Y-m-d H:i:s", time() + $token_data['expires_in'] - 100);

            $twitch_validate_data = $twitch_api->validateUser($token_data['access_token']);

            if (empty($twitch_validate_data['login'])) {
                return redirect(env('APP_URL'). '/login.php');
            }

            $user_data_array['twitch_username'] = GetCleanInput(strtolower($twitch_validate_data['login']));
            $user_data_array['twitch_userid'] = $twitch_validate_data['user_id'];

            $existingUser = User::where('twitch_userid', $twitch_validate_data['user_id'])->first();

            if ($existingUser) {
                $existingUser->update($user_data_array);
            } else {
                User::create($user_data_array);
            }

            $_SESSION['twitch_userid'] = $twitch_validate_data['user_id'];
            $_SESSION['twitch_username'] = $user_data_array['twitch_username'];
            return redirect(env('APP_URL'));
        }

        $page_title = 'irTwitch - Login';
        return view('login', compact('page_title'));
    }

    public function loginApp(Request $request)
    {
        $currentDomain = request()->getHttpHost();
        $desiredDomain = str_replace('https://', '', env('APP_URL'));

        if ($currentDomain !== $desiredDomain) {
            header('location: ' . env('APP_URL') . '/login_app.php');
            exit();
        }

        if (!empty($request->input('code'))) {
            $twitch_api = new TwitchAPI();
            $token_data = $twitch_api->GetToken(
                GetCleanInput($request->input('code')),
                MAIN_TWITCH_BOT_TOKEN['client_id'],
                MAIN_TWITCH_BOT_TOKEN['client_secret'],
                '/login_app.php'
            );

            if (empty($token_data['m_status']) || empty($token_data['access_token'])) {
                return redirect(env('APP_URL'). '/login_app.php');
            }

            $user_data_array['twitch_r'] = $token_data['refresh_token'];
            $user_data_array['twitch_a'] = $token_data['access_token'];
            $user_data_array['fcmToken'] = null;
            $user_data_array['twitch_expire'] = date("Y-m-d H:i:s", time() + $token_data['expires_in'] - 100);

            $twitch_validate_data = $twitch_api->validateUser($token_data['access_token']);

            if (empty($twitch_validate_data['login'])) {
                return redirect(env('APP_URL'). '/login.php');
            }

            $username = GetCleanInput(strtolower($twitch_validate_data['login']));
            $user = User::where('twitch_userid', $twitch_validate_data['user_id'])->first();
            $app_token = md5(time() . '_' . $twitch_validate_data['user_id'] . '_' . $username . '_' . time() . rand(1, 999999));
            $apptoken_date = date("Y-m-d H:i:s", time() + 5184000);
            
            if ($user) {
                $user_data_array['app_token'] = $app_token;
                $user_data_array['apptoken_date'] = $apptoken_date;

                User::where('twitch_userid', $twitch_validate_data['user_id'])->update($user_data_array);
            } else {
                $user_data_array['app_token'] = $app_token;
                $user_data_array['apptoken_date'] = $apptoken_date;
                $user_data_array['twitch_userid'] = $twitch_validate_data['user_id'];
                $user_data_array['twitch_username'] = $username;

                User::create($user_data_array);
            }

            $url_data = '?token=' . $app_token . '&twitch_username=' . $username . '&twitch_userid=' . $twitch_validate_data['user_id'];

            header('location: irtwapp://success' . $url_data);
            exit();
        }

        $page_title = 'irTwitch - App Login';
        return view('login_app', compact('page_title'));
    }

    public function logout(Request $request)
    {
        if (!empty($_SESSION['twitch_userid'])) {
            $twitch_api = new TwitchAPI();

            $token = $twitch_api->getUserTokenByUserID($_SESSION['twitch_userid']);
            if (!empty($token)) {
                User::where('twitch_userid', $_SESSION['twitch_userid'])
                    ->update([
                        'user_token' => '',
                        'twitch_a' => '',
                        'twitch_r' => ''
                    ]);

                $twitch_api->disconnect($token, MAIN_TWITCH_BOT_TOKEN['client_id']);
            }
        }

        unset($_SESSION['twitch_userid']);

        return redirect()->to(env('APP_URL') . '/login.php');
    }
}
