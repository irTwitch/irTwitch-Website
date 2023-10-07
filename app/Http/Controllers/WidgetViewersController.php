<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Streamer;

class WidgetViewersController extends Controller
{
    public function show(Request $request)
    {
        if (empty($request->input('streamer')) || is_array($request->input('streamer')) || !preg_match('/^[0-9a-zA-Z-_.]+$/', $request->input('streamer'))) {
            return "<p style='font-family: Tahoma; direction: rtl; text-align: center'>این استریمر در irTwitch ثبت نیست.</p>";
        }

        if (strtolower($request->input('streamer')) == 'amouranth') {
            return "<p style='font-family: Tahoma; direction: rtl; text-align: center; line-height:2;font-size:24px;'>خداییش انتظار نداشته باش آمار این یکیو داشته باشیم 😂<br>ما فقط آمار استریمرهایی که داخل سایت ثبت شدن رو داریم!</p>";
        }

        $streamer = Streamer::getStreamerByUsername($request->input('streamer'));

        if (!$streamer) {
            return "<p style='font-family: Tahoma; direction: rtl; text-align: center'>این استریمر در irTwitch ثبت نیست.</p>";
        }

        return view('widget', compact('streamer'));
    }
}
