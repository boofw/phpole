<?php namespace Boofw\Phpole\App;

use Boofw\Phpole\Http\Request;
use Boofw\Phpole\Http\Session;

class Auth
{
    static function id()
    {
        return Session::get('auth.id');
    }

    static function name()
    {
        return Session::get('auth.username');
    }

    static function user()
    {
        return Session::get('auth', []);
    }

    static function login($user)
    {
        Session::put('auth', $user);
        return self::user();
    }

    static function setReferer($referer = null, $force = false)
    {
        if ( ! $referer) {
            $referer = Request::refererInInput();
            if ($referer) {
                $force = true;
            } else {
                $referer = Request::referer();
            }
        }
        if (strpos($referer, '/auth/') !== false) {
            $referer = '/';
        }
        if ( ! Session::get('authreferer') || $force) {
            Session::put('authreferer', $referer);
        }
    }

    static function referer()
    {
        return Session::pull('authreferer', '/');
    }

    static function logout() {
        Session::forget('auth');
    }
}