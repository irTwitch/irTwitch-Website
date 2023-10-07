@include('header')
<div class="row pt-5 m-auto">
    <div class="col-12 col-sm-12 col-md-7 col-lg-8 col-xl-9 pb-3" style="direction: ltr;">
        <div class='videoPlayer'></div>
    </div>
    <div class="col-12 col-sm-12 col-md-5 col-lg-4 col-xl-3 pb-3">
        <div class="card chat-card text-center" style="min-height: 100%;">
            <div class="card-header">
                چت های استریم
            </div>
            <div class="card-body" style="min-height: 100%;">
                <ul id="messages">
                    @if (empty($user_chat_data['user_token']))
                        <li style="direction: rtl;text-align:center;color:#e07777">برای مشاهده چت ها و یا چت کردن باید در سایت لاگین کنید.</li>
                    @endif
                </ul>
            </div>
            <div class="card-footer text-muted directionrtl">
                <div class="input-group mb-3">
                    <input type="text" id="message_input" class="form-control" placeholder="متن پیام رو میتونی اینجا بنویسی ..." aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="button" id="send_message_btn">ارسال</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row pt-1 m-auto">
    <div class="col-12 col-sm-12 col-md-7 col-lg-8 col-xl-9 pt-2" style="direction: ltr;min-height: 100%;">
        <div class="card mb-3 card_streamtitle pb-2" style="background-color: transparent;min-height: 100%;">
            <div class="row g-0">
                <div class="col-3 col-sm-3 col-md-3 col-lg-2 col-xl-1 text-center">
                    <img src="{{ (!empty($streamer->twitch_profile_image_url)) ? str_replace('https://static-cdn.jtvnw.net/', site_url . 'images/', $streamer->twitch_profile_image_url) : '' }}" class="img-fluid rounded-circle" alt="{{ (!empty($streamer->twitch_display_name)) ? GetCleanInput($streamer->twitch_display_name) : '' }}">
                </div>
                <div class="col-9 col-sm-9 col-md-9 col-lg-8 col-xl-9">
                    <div class="card-body">
                        <h5 class="card-title h3 mb-0">{{ (!empty($streamer->twitch_display_name)) ? $streamer->twitch_display_name : '' }}
                            {!! (!empty($streamer->twitch_account_type) && $streamer->twitch_account_type === 'affiliate') ? ' <span class="bx bx-badge-check account_icon" style="color: #6f42c1;" data-toggle="tooltip" data-placement="top" title="Affiliate"></span>' : '' !!}
                            {!! (!empty($streamer->twitch_account_type) && $streamer->twitch_account_type === 'partner') ? ' <span class="bx bxs-badge-check account_icon" style="color: #6f42c1;" data-toggle="tooltip" data-placement="top" title="Partner"></span>' : '' !!}
                        </h5>
                        <p class="card-text">
                        <p class="card-text stream_title" style="line-height: 2; font-weight: bold;">{{ (!empty($streamer->twitch_title) && CheckTitle($streamer->twitch_title)) ? GetCleanInput($streamer->twitch_title) : ((!empty($streamer->twitch_display_name)) ? GetCleanInput($streamer->twitch_display_name) : '') }}</p>
                        <p class="card-text"><small class="text-muted2 game_title">{{ (!empty($game_data['title'])) ? $game_data['title'] : '' }}</small></p>
                        <p class="card-text mt-3" style="line-height: 1.2;"><small class="text-muted2">{{ (!empty($streamer->twitch_description) && CheckTitle($streamer->twitch_description)) ? GetCleanInput($streamer->twitch_description) : ((!empty($streamer->twitch_display_name)) ? GetCleanInput($streamer->twitch_display_name) : '') }}</small></p>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <p class="stream_views" data-toggle="tooltip" data-placement="top" title="irTwitch: {{ $stream_viewers }}<br>Twitch: {{ $twitch_views }}"><span class="bx bx-show" style="vertical-align: -2px;"></span> <span class="stream_views_number">{{ $total_views }}</span></p>
                            <p class="stream_timer_box mt-2"><span class="bx bx-time" style="vertical-align: -2px;"></span> <span class="stream_timer"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <a href="https://www.twitch.tv/{{ $streamer->username }}" target="_blank" class="btn btn-md btn-primary mt-0 directionrtl"><span class="bx bxl-twitch"></span> مشاهده استریم در توییچ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (!empty($streamer->stream_links))
        @php
            $stream_links = json_decode($streamer->stream_links, true);
        @endphp
        @if (!empty($stream_links))
            <div class="col-12 col-sm-12 col-md-5 col-lg-4 col-xl-3 pt-2" style="min-height: 100%;">
                <div class="card mb-3 card_streamtitle pb-2" style="background-color: transparent;min-height: 100%;">
                    @if (!empty($stream_links['donate']))
                        <a href="{{ $stream_links['donate'] }}" target="_blank" class="btn btn-md btn-donate  mt-0 directionrtl"><span class="bx bx-money"></span> حمایت مالی</a>
                    @endif
                    @if (!empty($stream_links['instagram']))
                        <a href="{{ $stream_links['instagram'] }}" target="_blank" class="btn btn-md btn-instagram  mt-2 directionrtl"><span class="bx bxl-instagram"></span> اینستاگرام</a>
                    @endif
                    @if (!empty($stream_links['youtube']))
                        <a href="{{ $stream_links['youtube'] }}" target="_blank" class="btn btn-md btn-youtube  mt-2 directionrtl"><span class="bx bxl-youtube"></span> یوتیوب</a>
                    @endif
                    @if (!empty($stream_links['discord']))
                        <a href="{{ $stream_links['discord'] }}" target="_blank" class="btn btn-md btn-discord  mt-2 directionrtl"><span class="bx bxl-discord"></span> دیسکورد</a>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>


@include('footer_js')
@if (!empty($user_chat_data['user_token']))
<script src="{{ site_url }}template/chat/socket.io.js"></script>
<script src="{{ site_url }}template/chat/twemoji.js?v2.1"></script>
<script src="{{ site_url }}template/chat/masterking32.js?4"></script>
@endif
<script type="text/javascript">
    var VideoURL = `@if (!empty($streamer->isLive)){{ site_url . 'live_streams/stream/' . GetCleanInput($streamer->username) . '.m3u8' }}@else{{ site_url . '404.m3u8' }}@endif`;
    var player = new Clappr.Player({
        source: VideoURL,
        parentId: ".videoPlayer",
        plugins: [LevelSelector],
        width: '100%',
        height: '100%',
        loop: true,
        mediacontrol: { buttons: "#dab0ff" },
        poster: `@if (!empty($streamer->isLive)){{ (!empty($streamer->twitch_thumbnail_url) ? GetCleanInput(str_replace('https://static-cdn.jtvnw.net/', site_url . 'images/', str_replace('{width}x{height}', '1280x720', $streamer->twitch_thumbnail_url))) : '') }}@else{{ (!empty($streamer->twitch_offline_image_url) ? GetCleanInput(str_replace('https://static-cdn.jtvnw.net/', site_url . 'images/', str_replace('{width}x{height}', '440x248', $streamer->twitch_offline_image_url))) : '') }}@endif`,
        levelSelectorConfig: {
            title: 'Quality',
            labelCallback: function(playbackLevel) {
                return playbackLevel.level.height + 'p';
            }
        },
        events: {
            onError: function(e) {
                @if (empty($streamer->isLive))
                    $('.player-error-screen').hide();
                    $('.play-wrapper').hide();
                    $('[player-error-screen__content]').hide();
                    return;
                @endif

                r--;
                var s = (r > 1) ? player.options.source : VideoURL;
                var t = 5;
                var retry = function() {
                    if (t === 0) {
                        var o = player.options;
                        o.source = s;
                        player.configure(o);
                        return;
                    }
                    Clappr.$('#retryCounter').text(t);
                    t--;
                    setTimeout(retry, 1000);
                };

                player.configure({
                    autoPlay: true,
                    source: 'playback.error',
                    playbackNotSupportedMessage: ' Retrying in <span id="retryCounter"></span> seconds ...'
                });

                retry();
            },
            onPlay: function(e) {
                $('[data-seekbar]').hide();
            }
        }
    });

    @if ($streamer->isLive == 1)
        refreshViews();
        let isss = 2;
        function refreshViews() {
            $.ajax('{{ site_url }}free_api.php?streamer={{ GetCleanInput($streamer->username) }}', {
                dataType: 'json',
                timeout: 10000,
                success: function(response, status, xhr) {
                    try {
                        if (response.error == 0 && response.data !== null && response.data.is_live === true) {
                            $(".game_title").html(response.data.game_title);
                            $(".stream_title").html(response.data.title);
                            $('.stream_views_number').jQuerySimpleCounter({
                                start: $('.stream_views_number').text(),
                                end: response.data.total_views,
                                duration: 4000
                            });
                            $(".stream_views").tooltip('hide').tooltip('dispose').attr('title', 'irTwitch: ' + response.data.irtwitch_viewers + '<BR>Twitch: ' + response.data.twitch_viewers).tooltip({html: true});
                        }
                    } catch (e) {}
                },
                error: function(jqXhr, textStatus, errorMessage) {}
            });
        }

        setInterval(function() {
            refreshViews();
        }, 25000);

        function get_elapsed_time_string(total_seconds) {
            function pretty_time_string(num) {
                return (num < 10 ? "0" : "") + num;
            }

            var hours = Math.floor(total_seconds / 3600);
            total_seconds = total_seconds % 3600;

            var minutes = Math.floor(total_seconds / 60);
            total_seconds = total_seconds % 60;

            var seconds = Math.floor(total_seconds);

            // Pad the minutes and seconds with leading zeros, if required
            hours = pretty_time_string(hours);
            minutes = pretty_time_string(minutes);
            seconds = pretty_time_string(seconds);

            // Compose the string for display
            var currentTimeString = hours + ":" + minutes + ":" + seconds;

            return currentTimeString;
        }

        var elapsed_seconds = {{ $stram_start }};
        setInterval(function() {
            elapsed_seconds = elapsed_seconds + 1;
            $('.stream_timer').text(get_elapsed_time_string(elapsed_seconds));
        }, 1000);
    @endif

    @if (!empty($user_chat_data['user_token']))
        const emoteManager = new EmoteManager();
        emoteManager.loadEmotes("{{ GetCleanInput($streamer->twitch_userid) }}");

        var socket = io("{{ site_url }}", { path: '/chat_service/server' });
        socket.on('connect', function(msg) {
            socket.emit('login', { username: "{{ GetCleanInput($user_chat_data['username']) }}", token1: "{{ $user_chat_data['user_token'] }}", token2: "{{ $user_chat_data['streamer_token'] }}", streamer: "{{ GetCleanInput($user_chat_data['streamer']) }}" });
        });

        var messages = document.getElementById('messages');

        var send_message_btn = document.getElementById('send_message_btn');
        var input = document.getElementById('message_input');
        send_message_btn.addEventListener("click", function(e) {
            if (input.value) {
                socket.emit('message', input.value);
                input.value = '';
            }
        });

        send_message_btn.addEventListener("click", function(e) {
            if (input.value) {
                socket.emit('message', input.value);
                input.value = '';
            }
        });

        input.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                if (input.value) {
                    socket.emit('message', input.value);
                    input.value = '';
                }
            }
        });

        socket.on('message', function(msg) {
            var item = document.createElement('li');
            item.innerHTML = emoteManager.parseTwitchEmoji(msg);
            messages.appendChild(item);
            messages.scrollTo(0, messages.scrollHeight);
            let chats = document.getElementById("messages").childElementCount;
            if (chats > 150) {
                messages.removeChild(messages.firstChild);
            }
        });
    @endif
</script>


@include('footer')
