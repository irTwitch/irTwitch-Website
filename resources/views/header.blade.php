@php
    define('site_url', env('APP_URL') . '/');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no">
    <title>{{ $page_title }}</title>
    <!-- Powered by MasterkinG32.CoM Designed by Amin Mahmoudi -->
    <meta name="description"
          content="پلتفرم نمایش توییچ فارسی و توییچ ایرانی، ایران توییچ (irTwitch - IranTwitch - irTW.live - irTW.FUN)."/>
    <meta name="keywords"
          content="استریم,توییچ, توییچ فارسی, Twitchfa,توییچ فا, ایران توییچ, TwitchFarsi, IrTwitch, IranTwitch, irTW"/>
    <meta name="generator" content="MasterkinG CMS"/>
    <meta property="og:locale" content=""/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="{{ site_url }}template/logo-200x200.png"/>
    <meta property="og:title" content="{{ $page_title }}"/>
    <meta property="og:description"
          content="پلتفرم نمایش توییچ فارسی و توییچ ایرانی، ایران توییچ (irTwitch - IranTwitch). 👍"/>
    <meta property="og:url" content="{{ site_url }}"/>
    <meta property="og:site_name"
          content="پلتفرم نمایش توییچ فارسی و توییچ ایرانی، ایران توییچ (irTwitch - IranTwitch)."/>
    <!-- Powered by MasterkinG32.CoM Designed by Amin Mahmoudi -->
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:description"
          content="پلتفرم نمایش توییچ فارسی و توییچ ایرانی، ایران توییچ (irTwitch - IranTwitch). 👍"/>
    <meta name="twitter:title" content="{{ $page_title }}"/>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ site_url }}favicon.ico">
    <link href="{{ site_url }}template/main_site/css/iransans.css" rel="stylesheet" type="text/css" />
    <link href="{{ site_url }}template/main_site/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="{{ site_url }}template/main_site/css/reset.css" rel="stylesheet">
    <link href="{{ site_url }}template/main_site/css/boxicons.min.css" rel="stylesheet">
    <link href="{{ site_url }}template/main_site/css/style.css" rel="stylesheet">
</head>
<body style="direction: rtl;">
    <div class="side-bar sidebar_collapse">
        <div class="logo-name-wrapper">
          <div class="logo-name">
            <img
              src="{{ site_url }}template/logo-small.png"
              class="logo"
              alt="irTwitch"
              srcset=""
            />
            <span class="logo-name__name">irTwitch</span>
          </div>
          <button class="logo-name__button" name='menu_button1'>
            <i
              class="bx bx-arrow-from-right logo-name__icon"
              id="logo-name__icon"
            ></i>
          </button>
        </div>

        <ul class="features-list">
            <li class="features-item draft">
                <a href="{{ site_url }}">
                    <i class="bx bx-home-alt features-item-icon"></i>
                    <span class="features-item-text">صفحه اصلی</span>
                </a>
            </li>
          <li class="features-item sent">
            <a href="{{ site_url }}add_streamer.php">
                <i class="bx bx-user-voice features-item-icon"></i>
                <span class="features-item-text">ثبت استریمر جدید</span>
            </a>
          </li>
          <li class="features-item trash">
            <a href="https://discord.gg/vYGQQaqG5X" target="_blank">
                <i class="bx bxl-discord-alt features-item-icon"></i>
                <span class="features-item-text">سرور دیسکورد</span>
            </a>
          </li>
          @if(!empty($_SESSION['twitch_userid']))
          <li class="features-item trash">
            <a href="{{ site_url }}logout.php">
                <i class="bx bx-user features-item-icon"></i>
                <span class="features-item-text">خروج از سایت</span>
            </a>
          </li>
          @else
          <li class="features-item trash">
            <a href="{{ site_url }}login.php">
                <i class="bx bx-user features-item-icon"></i>
                <span class="features-item-text">ورود به سایت</span>
            </a>
          </li>
          @endif
        </ul>
    </div>
    <div class="main_page main_page_collapse">
        <div class="container-fluid" style="direction: ltr;">
