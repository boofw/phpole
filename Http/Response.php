<?php namespace Polev\Phpole\Http;

use Polev\Phpole\Helper\Arr;

class Response
{
    static function redirect($s = null, $withInput = false, $flashData = [])
    {
        $flashData = Arr::dot($flashData);
        if ($withInput) {
            $flashData = Arr::add($flashData, '_old_input', $_REQUEST);
        }
        foreach ($flashData as $k=>$v) {
            Session::flash($k, $v);
        }

        if (!$s) {
            $s = Request::referer();
        }
        header('location: '.$s);
        return '<p>This page is moved to <a href="'.$s.'">'.$s.'</a></p>';
    }

    static function redirectPermanently($s = null)
    {
        header('HTTP/1.1 301 Moved Permanently');
        return self::redirect($s);
    }

    static function json($data = [])
    {
        header('Content-Type: application/x-javascript');
        return json_encode($data);
    }

    static function to($uri, $cmsg = [], $withInput = false)
    {
        if (Request::ajax()) {
            return self::json($cmsg);
        }
        return self::redirect($uri, $withInput, ['cmsg' => $cmsg]);
    }
}