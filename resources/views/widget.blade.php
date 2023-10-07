@php
    define('site_url', env('APP_URL') . '/');
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>irTwitch Viewers Widget</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: Orbitron;
            font-size: 52px;
        }

        .box {
            margin: auto;
            text-align: center;
        }

        .aparat-count {
            text-align: center;
            margin: auto;
        }

        img {
            width: 85px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="box">
    <img src="{{ site_url . 'template/logo-200x200.png' }}">
    <div id='irtwitch-count'>0</div>
    <img src="{{ site_url . 'template/twitch-logo.png' }}">
    <div id='twitch-count'>0</div>
</div>

<script src="{{ site_url . 'template/main_site/js/jquery-3.6.0.min.js' }}"></script>
<script>
(function($) {
    $.fn.jQuerySimpleCounter = function( options ) {
        let settings = $.extend({
            start:  0,
            end:    100,
            easing: 'swing',
            duration: 400,
            complete: ''
        }, options );

        const thisElement = $(this);

        $({count: settings.start}).animate({count: settings.end}, {
            duration: settings.duration,
            easing: settings.easing,
            step: function() {
                let mathCount = Math.ceil(this.count);
                thisElement.text(mathCount);
            },
            complete: settings.complete
        });
    };
}(jQuery));


function refreshViews()
{
    $.ajax('{{ site_url . 'free_api.php?streamer=' . GetCleanInput($streamer->username)}}',
    {
        dataType: 'json',
        timeout: 10000,
        success: function (response,status,xhr) {
            try{
                if(response.error == 0 && response.data !== null && response.data.is_live === true)
                {
                    $('#irtwitch-count').jQuerySimpleCounter({
                        start: $('#irtwitch-count').text(),
                        end: response.data.irtwitch_viewers,
                        duration: 4000
                    });
                    $('#twitch-count').jQuerySimpleCounter({
                        start: $('#twitch-count').text(),
                        end: response.data.twitch_viewers,
                        duration: 4000
                    });
                } else {
                    $('#irtwitch-count').jQuerySimpleCounter({
                        start: $('#irtwitch-count').text(),
                        end: 0,
                        duration: 4000
                    });
                    $('#twitch-count').jQuerySimpleCounter({
                        start: $('#twitch-count').text(),
                        end: 0,
                        duration: 4000
                    });
                }
            }catch(e){}
        },
        error: function (jqXhr, textStatus, errorMessage) {}
    });
}

refreshViews();
setInterval(function(){
    refreshViews();
}, 60000);
</script>
</body>
</html>
