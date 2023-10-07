<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', 'HomeController@index');
$router->get('/add_streamer.php', function () {
    return view('add_streamer');
});

$router->get('/widget.php', 'WidgetViewersController@show');
$router->post('/twitch_webhook_callback.php', 'TwitchWebhookController@callback');
$router->get('/sitemap.xml', 'SitemapController@index');
$router->get('/logout.php', 'AuthController@logout');
$router->get('/login.php', 'AuthController@login');
$router->get('/login_app.php', 'AuthController@loginApp');
$router->post('/discord_bot_response_api.php', 'ApiController@handleDiscordBotRequest');
$router->get('/instagram_story.php', 'ImageCreator@InstagramStory');
$router->get('/free_api.php', 'ApiController@free_api');
$router->get('/fcheck.php', 'ApiController@FilteringCheck');
$router->post('/fcheck.php', 'ApiController@FilteringCheck');
$router->get('/cron_ttoken.php', 'CronjobController@KasadaToken');
$router->get('/main_cronjob.php', 'CronjobController@MainCronJob');

$router->group(['prefix' => 'app_api'], function () use ($router) {
    $router->post('/logout', 'AppAPIController@logout');
    $router->post('/update-fcm-token', 'AppAPIController@updateFCMToken');
    $router->post('/follow-streamer', 'AppAPIController@followStreamer');
    $router->get('/user-follows', 'AppAPIController@userFollows');
});

$router->get('/masterking32_test.php', 'DevelopmentController@test');
$router->get('/{username}', ['uses' => 'StreamViewsController@viewStream']);

