<?php namespace Boofw\Phpole\Api;

use Exception;
use ArrayObject;

class Native
{
    static $apiDir = '';
    static $apiNamespace = '';

    private $api = '';

    function __construct($api)
    {
        $this->api = ucfirst($api);
    }

    function __call($name , $arguments)
    {
        $c = $this->api;
        if ( ! class_exists($c)) {
            if ( ! self::$apiDir) {
                self::$apiDir = dirname(dirname(dirname(dirname(__DIR__)))).'/api';
            }
            if (file_exists(self::$apiDir.'/'.$this->api.'.php')){
                require self::$apiDir.'/'.$this->api.'.php';
            }
        }
        if ( ! class_exists($c) && self::$apiNamespace) {
            $c = self::$apiNamespace . $this->api;
        }
        if ( ! class_exists($c)) {
            return new ArrayObject(array(
                'error' => 404,
                'message' => 'Api['.$c.'] Not Found',
                'data' => '',
            ), ArrayObject::ARRAY_AS_PROPS);
        }
        try {
            $r = new ArrayObject(array(
                'error' => 0,
                'message' => '',
                'data' => call_user_func_array(array($c, $name), $arguments),
            ), ArrayObject::ARRAY_AS_PROPS);
        } catch (Exception $e) {
            $r = new ArrayObject(array(
                'error' => $e->getCode()?:1,
                'message' => $e->getMessage(),
                'data' => '',
            ), ArrayObject::ARRAY_AS_PROPS);
        }
        return $r;
    }
}