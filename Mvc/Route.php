<?php namespace Boofw\Phpole\Mvc;

class Route
{
    static $routes = [];

    static $controller = '';
    static $action = '';
    static $route = '';

    static function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $appPath = config('app.path', dirname($_SERVER['PHP_SELF']));
        if ($appPath !== DIRECTORY_SEPARATOR && str_contains($uri, $appPath)) {
            $uri = substr($uri, strlen($appPath));
        }
        list($uri,) = explode('?', $uri);

        $routes = array(
            '/^\/(\d+)(\/.*)?$/' => 'c=index&a=show&id=$1&v=$2',
            '/^\/([\w]+)\/(\d+)(\/.*)?$/' => 'c=$1&a=show&id=$2&v=$3',
            '/^\/([\w]+)\/([\w]+)\/(\d+)(\/.*)?$/' => 'c=$1&a=$2&id=$3&v=$4',
            '/^\/([\w]+)\/([\w%]+)(\/.*)?$/' => 'c=$1&a=$2&v=$3',
            '/^\/([\w]+)\/?$/' => 'c=$1&a=index&v=',
        );
        if (is_array(self::$routes)) {
            $routes = array_merge(self::$routes, $routes);
            foreach (self::$routes as $k => $v) {
                $routes[$k] = $v;
            }
        }

        foreach ($routes as $rk=>$rv) {
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
                        unset($args['v']);
                        for ($i=0; $i<count($moreArgs); $i=$i+2) {
                            if ( ! is_numeric($moreArgs[$i]) && ! (preg_match('/^\$[1-9]$/', $moreArgs[$i]) && ! $moreArgs[$i+1])) {
                                $args[$moreArgs[$i]] = $moreArgs[$i+1];
                            }
                        }
                    }
                    $_GET = array_merge($_GET, $args);
                    $_REQUEST = array_merge($_REQUEST, $args);
                }
                break;
            }
        }
        if ( ! self::$controller) self::$controller = 'index';
        if ( ! self::$action) self::$action = 'index';
        self::$route = self::$controller.'/'.self::$action;
        echo Controller::run();
    }
}