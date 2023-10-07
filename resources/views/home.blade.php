@include('header')

<div class="row pt-4 m-auto" style="direction: rtl;">
    <h1 style="font-size: 24px;"><span class='bx bx-broadcast color-highlight' style="font-size: 28px; vertical-align: -5px;"></span> استریم های درحال پخش  <span class="badge" style="font-size: 14px;direction:ltr;background-color: rgba(169, 112, 255, 0.2);"><span class="bx bx-play-circle" style="font-size:20px; vertical-align: -5px;"></span> {{ count($result) }} - <span class="bx bx-show" style="font-size:20px; vertical-align: -5px;"></span> {{ $all_views }} -  <img src='{{ site_url }}/template/logo_sm.png' alt='irtw logo' style='max-width:16px;vertical-align: -3px;'> {{ $all_viewsIR }}</span></h1>
    <div class="bar mb-3 mt-4"></div>
</div>
<!-- @if(empty($_COOKIE['HideAlert1']))
<div class="alert alert-warning alert-dismissible fade show directionrtl" role="alert" style="line-height: 2;">
    <strong class='fw-bold'>درود بر تو ای نسل غیور جامانده!</strong> با توجه به احتمال فیلترشدن سایت، جهت دریافت آدرس جدید، لطفا در <a href='https://t.me/irtwitch' class="link-info fw-bold" target="_blank">تلگرام</a> و <a href='https://discord.gg/vYGQQaqG5X' class="link-danger fw-bold" target="_blank">سرور دیسکورد</a> ما حضور داشته باش و اگه میتونی سایت رو به دوستات هم معرفی کن!
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2 mb-1 border-bottom"></div>
    <strong class='fw-bold'>استریمرها،</strong> از طریق <a href='https://discord.gg/vYGQQaqG5X' target="_blank" class='fw-bold'>سرور دیسکورد ما</a> میتوانند لینک های زیر استریمشون رو ویرایش کنن!
    <button type="button" class="btn-close closeMessageF" data-bs-dismiss="alert" aria-label="Close" style="padding-top: 17px;"></button>
</div>
@endif -->
@if(empty($_COOKIE['HideAlert11']))
<div class="alert alert-danger alert-dismissible fade show directionrtl" role="alert" style="line-height: 2;">
    <strong class='fw-bold'>درود 👋</strong> نسخه جدید اپلیکیشن اندروید iRTwitch منتشر شد.
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2 mb-1 border-bottom"></div>
    جهت دانلود به کانال <a href='https://t.me/irtwitch/34' class="link-info fw-bold" target="_blank"><svg id="svg2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" width="20" height="20"><style>.st0{fill:url(#path2995-1-0_1_)}.st1{fill:#c8daea}.st2{fill:#a9c9dd}.st3{fill:url(#path2991_1_)}</style><linearGradient id="path2995-1-0_1_" gradientUnits="userSpaceOnUse" x1="-683.305" y1="534.845" x2="-693.305" y2="511.512" gradientTransform="matrix(6 0 0 -6 4255 3247)"><stop offset="0" stop-color="#37aee2"/><stop offset="1" stop-color="#1e96c8"/></linearGradient><path id="path2995-1-0" class="st0" d="M240 120c0 66.3-53.7 120-120 120S0 186.3 0 120 53.7 0 120 0s120 53.7 120 120z"/><path id="path2993" class="st1" d="M98 175c-3.9 0-3.2-1.5-4.6-5.2L82 132.2 152.8 88l8.3 2.2-6.9 18.8L98 175z"/><path id="path2989" class="st2" d="M98 175c3 0 4.3-1.4 6-3 2.6-2.5 36-35 36-35l-20.5-5-19 12-2.5 30v1z"/><linearGradient id="path2991_1_" gradientUnits="userSpaceOnUse" x1="128.991" y1="118.245" x2="153.991" y2="78.245" gradientTransform="matrix(1 0 0 -1 0 242)"><stop offset="0" stop-color="#eff7fc"/><stop offset="1" stop-color="#fff"/></linearGradient><path id="path2991" class="st3" d="M100 144.4l48.4 35.7c5.5 3 9.5 1.5 10.9-5.1L179 82.2c2-8.1-3.1-11.7-8.4-9.3L55 117.5c-7.9 3.2-7.8 7.6-1.4 9.5l29.7 9.3L152 93c3.2-2 6.2-.9 3.8 1.3L100 144.4z"/></svg> تلگرام</a> ما مراجعه کنید. (28 خرداد 1402)
    <button type="button" class="btn-close closeMessageF2" data-bs-dismiss="alert" aria-label="Close" style="padding-top: 17px;"></button>
</div>
@endif

<div class="row pt-5 m-auto">
@foreach($result as $stream)
    @php
        $game_data = null;
        if (!empty($stream['twitch_category_id'])) {
            $game_data = $twitch_api->Bot_Get_Game($stream['twitch_category_id']);
        }

    @endphp
<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-2 pb-3">
    <div class="card card-custom border-darkcard border-0">
        <div class="card-custom-img" style="background-image: url('{{ !empty($stream['twitch_thumbnail_url']) ? GetCleanInput(str_replace('https://static-cdn.jtvnw.net/', site_url . 'images/', str_replace('{width}x{height}', '313x140', $stream['twitch_thumbnail_url']))) : '' }}');"></div>
        <span class="badge stream_views">
            <i class="bx bx-show"></i>
            {{ !empty($stream['total_views']) ? $stream['total_views'] : '0' }} =
            <img src="{{ site_url . 'template/twitch-logo.png' }}" alt="twitch logo" style="max-width:13px;vertical-align: -2px;">
            {{ !empty($stream['twitch_viewers']) ? $stream['twitch_viewers'] : '0' }} +
            <img src="{{ site_url . 'template/logo_sm.png' }}" alt="irtw logo" style="max-width:13px;vertical-align: -2px;">
            {{ !empty($stream['stream_viewers']) ? $stream['stream_viewers'] : '0' }}
        </span>
        @if (!empty($stream['is_relay']))
            <span class="badge irtwitch_support">
                <img src="{{ site_url . 'template/logo_sm.png' }}" alt="irtw logo">
            </span>
        @endif
        <div class="card-custom-avatar">
            <img class="img-fluid" src="{{ !empty($stream['twitch_profile_image_url']) ? GetCleanInput(str_replace('https://static-cdn.jtvnw.net/', site_url . 'images/', str_replace('300x300', '70x70', str_replace('{width}x{height}', '70x70', $stream['twitch_profile_image_url'])))) : '' }}" alt="Avatar" />
        </div>
        <div class="card-body" style="overflow-y: auto">
            <p class="card-text">{{ (!empty($stream['twitch_title']) && CheckTitle($stream['twitch_title'])) ? GetCleanInput($stream['twitch_title']) : (!empty($stream['twitch_display_name']) ? GetCleanInput($stream['twitch_display_name']) : '') }}</p>
            <h6 class="card-title mb-2 mt-2 text-color1">{{ !empty($stream['twitch_display_name']) ? GetCleanInput($stream['twitch_display_name']) : '' }}</h6>
            <p class="card-title mb-0 mt-2 text-color1">{{ !empty($game_data['title']) ? GetCleanInput($game_data['title']) : '' }}</p>
        </div>
        <a class="card-link" href="{{ !empty($_SESSION['twitch_userid']) ? site_url . GetCleanInput($stream['username']) : 'https://www.twitch.tv/' . GetCleanInput($stream['username']) }}" target="_blank"'>
            {{ !empty($stream['username']) && function_exists('GetCleanInput') ? GetCleanInput($stream['username']) : $stream['username'] }}
        </a>
    </div>
</div>

@endforeach

</div>

@include('footer_js')
<script>
$(function() {
    //   const showAlert = localStorage.getItem("alert") === null;
    //   $(".alert").toggleClass("d-none",!showAlert)
    $(".closeMessageF").on("click",function() {
        var now = new Date();
        now.setHours(now.getHours() + 24);
        document.cookie = "HideAlert1=1; expires=" + now + "; path=/";
        $(this).parent().addClass("d-none");
    });
    $(".closeMessageF2").on("click",function() {
        var now = new Date();
        now.setHours(now.getHours() + 24);
        document.cookie = "HideAlert11=1; expires=" + now + "; path=/";
        $(this).parent().addClass("d-none");
    });
});
</script>
@include('footer')
