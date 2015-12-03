<?php namespace Boofw\Phpole\Mvc;

use Boofw\Phpole\Http\Response;
use Boofw\Phpole\App\Auth;
use Boofw\Phpole\Helper\Arr;

class Filter
{
    static $filters = [];

    static function init()
    {
        if (Arr::get(self::$filters, 'auth') instanceof Closure) {
            return false;
        }
        self::add('auth', function(){
            if ( ! Auth::id()) {
                die(Response::redirect(url('auth/login')));
            }
        });
        self::add('guest', function(){
            if (Auth::id()) {
                die(Response::redirect(Auth::referer()));
            }
        });
    }

    static function run($filter)
    {
        self::init();
        $func = Arr::get(self::$filters, $filter);
        return $func();
    }

    static function add($filter, $func)
    {
        Arr::set(self::$filters, $filter, $func);
    }
}