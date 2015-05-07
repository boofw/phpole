<?php namespace Polev\Phpole\App;

use Polev\Phpole\Http\Request;
use Polev\Phpole\Http\Session;

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
        return Session::get('auth');
    }

    static function login($user)
    {
        Session::put('auth', $user);
        return self::user();
    }

    static function setReferer($referer = null, $force = false)
    {
        if ( ! $referer) $referer = Request::referer();
        if ( ! Session::get('auth.referer') || $force) {
            Session::put('auth.referer', $referer);
        }
    }

    static function referer()
    {
        return Session::pull('auth.referer');
    }

    static function logout() {
        Session::forget('auth');
    }
}