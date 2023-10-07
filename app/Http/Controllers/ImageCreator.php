<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Streamer;
use Illuminate\Support\Facades\App;

class ImageCreator extends Controller
{

    public function InstagramStory(Request $request)
    {
        $streamer = $request->input('streamer');
        if (empty($streamer) || is_array($streamer) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $streamer)) {
            return 'Streamer is not valid.';
        }

        $streamer = strtolower($streamer);
        $result = Streamer::where('username', $streamer)
            ->select(['twitch_userid', 'username', 'twitch_profile_image_url'])
            ->get();

        if ($result->isEmpty()) {
            $result = Streamer::where('discord_userid', $streamer)
                ->select(['twitch_userid', 'username', 'twitch_profile_image_url'])
                ->get();

            if ($result->isEmpty()) {
                return 'Streamer is not valid.';
            }
        }

        $url = str_replace('300x300', '600x600', $result[0]['twitch_profile_image_url']);
        $avatar = imagecreatefrompng ( $url );
        $resized_image = imagecreatetruecolor(445,445);
        imagecopyresampled ( $resized_image, $avatar, 0, 0, 0, 0, 445, 445, imagesx ( $avatar ), imagesy ( $avatar ) );

        $crop = new CircleCrop($resized_image);
        $dir = base_path('public');

        $sep = DIRECTORY_SEPARATOR ;
        $background = imagecreatetruecolor(1080, 1920);
        $firstUrl =  $dir . $sep . 'uploads'. $sep . 'irtwitch-layer.png';
        $outputImage = $background;
        $first = imagecreatefrompng($firstUrl);
        $second = $crop->crop()->getImg();

        imagecopymerge($outputImage,$first,0,0,0,0, 1080, 1920,100);
        imagecopymerge($outputImage,$second,307,102, 0,0, 445, 445,100);

        $image_width = imagesx($outputImage);
        $image_height = imagesy($outputImage);

        $font_size = 30; $x = 65; $y = 990; $angle = 0; $quality = 100;

        $font = $dir . $sep . 'uploads'. $sep . 'font.ttf';

        $text = str_replace('https://', '' , str_replace('http://', '' , env('APP_URL') )) . "/" . $result[0]['username'];
        $text_box = imagettfbbox($font_size,$angle,$font, $text);

        $text_width = $text_box[2]-$text_box[0];
        $text_height = $text_box[7]-$text_box[1];
        $x = ($image_width/2) - ($text_width/2) - 10;
        $y = 982;

        $black = imagecolorallocate($outputImage, 0, 0, 0);
        imagettftext($outputImage, $font_size, 0, $x, $y, $black, $font, $text);
        header("Content-type: image/png");
        $seconds_to_cache = 6000;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        imagepng($outputImage);

    }

}

class CircleCrop
{
    private $src_img;
    private $src_w;
    private $src_h;
    private $dst_img;
    private $dst_w;
    private $dst_h;

    public function __construct($img)
    {
        $this->src_img = $img;
        $this->src_w = imagesx($img);
        $this->src_h = imagesy($img);
        $this->dst_w = imagesx($img);
        $this->dst_h = imagesy($img);
    }

    public function display()
    {
        header("Content-type: image/png");
        imagepng($this->dst_img);
        return $this;
    }

    public function reset()
    {
        if (is_resource(($this->dst_img)))
        {
            imagedestroy($this->dst_img);
        }
        $this->dst_img = imagecreatetruecolor($this->dst_w, $this->dst_h);
        imagecopy($this->dst_img, $this->src_img, 0, 0, 0, 0, $this->dst_w, $this->dst_h);
        return $this;
    }

    public function size($dstWidth, $dstHeight)
    {
        $this->dst_w = $dstWidth;
        $this->dst_h = $dstHeight;
        return $this->reset();
    }

    public function crop()
    {
        // Intializes destination image
        $this->reset();

        // Create a black image with a transparent ellipse, and merge with destination
        $mask = imagecreatetruecolor($this->dst_w, $this->dst_h);
        $maskTransparent = imagecolorallocate($mask, 255, 0, 255);
        imagecolortransparent($mask, $maskTransparent);
        imagefilledellipse($mask, $this->dst_w / 2, $this->dst_h / 2, $this->dst_w, $this->dst_h, $maskTransparent);
        imagecopymerge($this->dst_img, $mask, 0, 0, 0, 0, $this->dst_w, $this->dst_h, 100);

        // Fill each corners of destination image with transparency
        $dstTransparent = imagecolorallocate($this->dst_img, 255, 0, 255);
        imagefill($this->dst_img, 0, 0, $dstTransparent);
        imagefill($this->dst_img, $this->dst_w - 1, 0, $dstTransparent);
        imagefill($this->dst_img, 0, $this->dst_h - 1, $dstTransparent);
        imagefill($this->dst_img, $this->dst_w - 1, $this->dst_h - 1, $dstTransparent);
        imagecolortransparent($this->dst_img, $dstTransparent);

        return $this;
    }

    public function getImg() {
        return $this->dst_img;
    }
}
