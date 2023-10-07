<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;
use App\Models\Streamer;

class SitemapController extends Controller
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
        // Try to retrieve the cached sitemap content
        $cachedSitemap = Cache::get('sitemap');
        if (!is_null($cachedSitemap)) {
            return response($cachedSitemap)->header('Content-Type', 'application/xml');
        }

        $streamers = Streamer::where('is_relay', 1)
            ->where('isBan', 0)
            ->get(['username', 'add_date']);

        $content = View::make('sitemap', ['streamers' => $streamers])->render();

        // Store the sitemap content in cache for 1 hour
        $expiration = Carbon::now()->addHour();
        Cache::put('sitemap', $content, $expiration);

        return response($content)->header('Content-Type', 'application/xml');
    }
}
