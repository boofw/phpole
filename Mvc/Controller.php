<?php namespace Boofw\Phpole\Mvc;

use Boofw\Phpole\Helper\Arr;
use Boofw\Phpole\Exception\HttpException;
use Boofw\Phpole\Exception\AppException;

class Controller
{
    static $controllerDir = '';
    static $namespaces = [];

    protected $filters = [];

    function __construct()
    {
        $this->filter();
        $this->before();
    }

    function __destruct()
    {
        $this->after();
    }

    function __call($func, $args)
    {
        if (preg_match('/^(get|post)[A-Z][a-z]*$/', $func)) {
            throw new HttpException(404);
        } else {
            throw new AppException('Method '.__CLASS__.'::'.$func.' not found!');
        }
    }

    function before()
    {
    }

    function after()
    {
    }

    function filter($action = '')
    {
        $filters = Arr::get($this->filters, $action, []);
        foreach ($filters as $v) {
            Filter::run($v);
        }
    }

    static function run()
    {
        $c = self::newInstance();
        if ($c instanceof self) {
            $a = 'get' . ucfirst(Route::$action);
            if (Arr::get($_SERVER, 'REQUEST_METHOD')==='POST') {
                $a = 'post' . ucfirst(Route::$action);
            }
            $c->filter($a);
            echo $c->$a();
        } else {
            throw new HttpException(404);
        }
    }

    static function newInstance()
    {
        if ( ! self::$controllerDir) {
            self::$controllerDir = dirname(dirname(dirname(dirname(__DIR__)))).'/controller';
        }

        $c = ucfirst(Route::$controller) . 'Controller';
        if ( ! class_exists($c) && file_exists(self::$controllerDir.'/'.$c.'.php')) {
            require self::$controllerDir.'/'.$c.'.php';
        }
        if (class_exists($c)) {
            return new $c();
        }
        foreach (self::$namespaces as $ns) {
            $c = $ns.$c;
            if (class_exists($c)) {
                return new $c();
            }
        }
        return null;
    }
}