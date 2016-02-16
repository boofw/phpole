<?php namespace Boofw\Phpole\Http;

use Boofw\Phpole\Helper\Arr;

class Response
{
    static function redirect($uri = null, $withInput = false, $flashData = [])
    {
        Session::reflash();
        if ($withInput) {
            Input::flash();
        }
        foreach ($flashData as $k=>$v) {
            Session::flash($k, $v);
        }

        if (!$uri) {
            $uri = Request::referer();
        }
        header('location: '.$uri);
        return '<p>This page is moved to <a href="'.$uri.'">'.$uri.'</a></p>';
    }

    static function redirectPermanently($uri = null)
    {
        header('HTTP/1.1 301 Moved Permanently');
        return self::redirect($uri);
    }

    static function json($data = [])
    {
        header('Content-Type: application/x-javascript');
        return json_encode($data);
    }

    static function to($error, $message, $uri = null, $withInput = false, $data = [])
    {
        $cmsg = compact('error', 'message', 'uri', 'data');
        if (Request::ajax()) {
            return self::json($cmsg);
        }
        return self::redirect($uri, $withInput, ['cmsg' => $cmsg]);
    }

    static function success($message, $uri = null, $withInput = false, $data = [])
    {
        return self::to(0, $message, $uri, $withInput, $data);
    }

    static function error($message, $uri = null, $withInput = false, $data = [])
    {
        return self::to(1, $message, $uri, $withInput, $data);
    }
}