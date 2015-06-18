<?php namespace Polev\Phpole\Mvc;

use Polev\Phpole\Helper\Arr;
use Polev\Phpole\Exception\HttpException;
use Polev\Phpole\Exception\AppException;

class Controller
{
    static $controllerDir = '';

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
        $c = ucfirst(Route::$controller) . 'Controller';
        if (!class_exists($c) && file_exists(self::$controllerDir.'/'.$c.'.php')) {
            require self::$controllerDir.'/'.$c.'.php';
        }
        if (class_exists($c)) {
            $a = 'get' . ucfirst(Route::$action);
            if (Arr::get($_SERVER, 'REQUEST_METHOD')==='POST') {
                $a = 'post' . ucfirst(Route::$action);
            }
            $c = new $c();
            $c->filter($a);
            echo $c->$a();
        } else {
            throw new HttpException(404);
        }
    }
}