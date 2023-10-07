<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@php
    define('site_url', env('APP_URL') . '/');
@endphp
<url>
  <loc>{{ site_url }}</loc>
  <lastmod>{{ date('Y-m-d\TH:i:sP', time()) }}</lastmod>
</url>
<url>
  <loc>{{ site_url }}add_streamer.php</loc>
  <lastmod>2022-07-25T01:01:41+04:30</lastmod>
</url>
<url>
  <loc>{{ site_url }}login.php</loc>
  <lastmod>2022-07-25T01:01:00+04:30</lastmod>
</url>
@foreach ($streamers as $streamer)
<url>
    <loc>{{ site_url . $streamer->username }}</loc>
    <lastmod>{{ date('Y-m-d\TH:i:sP', strtotime($streamer->add_date)) }}</lastmod>
</url>
@endforeach
</urlset>
