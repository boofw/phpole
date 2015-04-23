<?php namespace Polev\Phpole\Mvc;

class Controller
{
    static $controllerDir = '';

    function __construct()
    {
        $this->before();
    }

    function __destruct()
    {
        $this->after();
    }

    function before()
    {
    }

    function after()
    {
    }

    static function show($data = [])
    {
        return View::render(Route::$controller.'/'.Route::$action, $data);
    }

    static function run()
    {
        $c = ucfirst(Route::$controller) . 'Controller';
        if (!class_exists($c) && file_exists(self::$controllerDir.'/'.$c.'.php')) {
            require self::$controllerDir.'/'.$c.'.php';
        }
        if (class_exists($c)) {
            $a = 'get' . ucfirst(Route::$action);
            $c = new $c();
            echo $c->$a();
        } else {
            echo 404;
        }
    }
}