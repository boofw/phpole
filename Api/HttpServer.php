<?php namespace Polev\Phpole\Api;

class HttpServer
{
    static $keys = [];

    static function run()
    {
        if (self::makeToken($_SERVER['HTTP_PHPOLE_APPID']) !== $_SERVER['HTTP_PHPOLE_TOKEN']) {
            return json_encode(['error' => 'token验证失败']);
        }
        $r = call_user_func_array(array(api(input('api')), input('func')), json_decode(input('arguments'), 1));
        return json_encode($r);
    }

    static function makeToken($appId)
    {
        $appKey = array_get(self::$keys, $appId);
        $post = $_POST;
        $token = sha1($appId.$appKey.$post['api'].$post['func'].md5(json_encode($post['arguments'])));
        return $token;
    }
}