<?php namespace Polev\Phpole\Api;

use Polev\Phpole\Http\Client;
use ArrayObject;

class HttpClient
{
    static $url = '';
    static $appId = '';
    static $appKey = '';

    private $api = '';

    function __construct($api)
    {
        $this->api = ucfirst($api);
    }

    function __call($name , $arguments)
    {
        $post = array(
            'api' => $this->api, 'func' => $name, 'arguments' => json_encode($arguments),
        );
        $r = Client::post(self::$url, $post, array(
            'PHPOLE-APPID: ' . self::$appId,
            'PHPOLE-TOKEN: ' . self::makeToken($post),
        ));
        // print_r($r); var_dump($r);
        return new ArrayObject(json_decode($r, 1), ArrayObject::ARRAY_AS_PROPS);
    }

    static function makeToken($post)
    {
        $appId = self::$appId;
        $appKey = self::$appKey;
        $token = sha1($appId.$appKey.$post['api'].$post['func'].md5(json_encode($post['arguments'])));
        return $token;
    }
}