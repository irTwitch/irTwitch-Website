<?php
$expiration = 30 * 24 * 60 * 60;
ini_set('session.cookie_lifetime', $expiration);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_start();

date_default_timezone_set('Asia/Tehran');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header('X-Powered-Framework: MasterkinG-Framework');
header('X-Powered-CMS: MasterkinG-CMS');

define('filtered_domains', ['XXXXXXXXXXXXXXXX.cfd']);
define('site_url_webhook', 'https://XXXXXXXXXXXXXXXX.fun/');
define('app_domain', 'https://XXXXXXXXXXXXXXXX.fun/');
define('min_app_version', '1.0.0');
define('api_valid_clients', ['XXXXXXXXXXXXXXXXXXXXXXXXXXX']);

define('TWITCH_BOTS_TOKEN', array(
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
	['client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'],
));

define('MAIN_TWITCH_BOT_TOKEN', array('client_id' => 'XXXXXXXXXXXXXXXX', 'client_secret' => 'XXXXXXXXXXXXXXXX'));

define('TWITCH_SUBEVENT_SECRET', 'XXXXXXXXXXXXXXXX');
define('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0');

function GetCleanInput($input) {
    if (empty($input)) {
        return false;
    }

    // Trim leading and trailing whitespace
    $input = trim($input);

    // Convert special characters to HTML entities
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Prevent null byte injection
    $input = str_replace(chr(0), '', $input);

    return $input;
}

function GetUserIP()
{
    $ip = false;

    // Check for Cloudflare headers
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($ips as $address) {
            $address = trim($address);
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ip = $address;
                break;
            }
        }
    }

    // If Cloudflare headers are not present or no IPv6 address found, fallback to reverse proxy headers or remote address
    if (empty($ip)) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ips as $address) {
                $address = trim($address);
                if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    $ip = $address;
                    break;
                }
            }
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }

    return $ip;
}

function CheckTitle($string) {
    $keywords = array(
        'FUCK',
        'BITCH'
    );

    $normalizedString = strtolower($string);
    foreach ($keywords as $keyword) {
        // Escape the keyword for safe use in regular expression
        $escapedKeyword = preg_quote($keyword, '/');

        // Create a case-insensitive regular expression pattern
        $pattern = "/$escapedKeyword/i";

        // Perform the pattern matching
        if (preg_match($pattern, $normalizedString)) {
            return false;
        }
    }

    return true;
}


require_once dirname(realpath(__FILE__)) . '/jdf.php';
