<?php namespace Polev\Phpole\Mvc;

class Route
{
    static $routes = [];

    static $controller = '';
    static $action = '';
    static $route = '';

    static function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $root = dirname($_SERVER['DOCUMENT_URI']);
        if ($root != '/') {
            $uri = substr($uri, strlen($root));
        }
        if (!is_array(self::$routes)) {
            self::$routes = array();
        }
        self::$routes['/^\/([\w]+)\/([\w]+)([\/\?]{1}.*)?$/'] = 'c=$1&a=$2&v=$3';
        self::$routes['/^\/([\w]+)([\/\?]{1}.*)?$/'] = 'c=$1&a=index&v=$2';
        foreach (self::$routes as $rk=>$rv) {
            if (preg_match($rk, $uri, $m)) {
                foreach ($m as $mk=>$mv) {
                    if ($mk>0) $rv = str_replace('$'.$mk, $mv, $rv);
                }
                parse_str($rv, $args);
                if (is_array($args)) {
                    self::$controller = $args['c'];
                    self::$action = $args['a'];
                    unset($args['c']);
                    unset($args['a']);
                    if ($args['v']) {
                        $moreArgs = explode('/', trim($args['v'], '/'));
                        for ($i=0; $i<count($moreArgs); $i=$i+2) {
                            if (!is_numeric($moreArgs[$i]) && !(preg_match('/^\$[1-9]$/', $moreArgs[$i]) && !$moreArgs[$i+1])) {
                                $args[$moreArgs[$i]] = $moreArgs[$i+1];
                            }
                        }
                    }
                    unset($args['v']);
                    $_GET = array_merge($_GET, $args);
                }
                break;
            }
        }
        if (!self::$controller) self::$controller = 'index';
        if (!self::$action) self::$action = 'index';
        self::$route = self::$controller.'/'.self::$action;
        echo Controller::run();
    }
}