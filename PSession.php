<?php
class PSession
{
    static function put($k, $v)
    {
        $_SESSION[$k] = $v;
    }

    static function flash($k, $v)
    {
        return self::put('_flash.'.$k, $v);
    }

    static function has($k)
    {
        return isset($_SESSION[$k]) || isset($_SESSION['_flash.'.$k]);
    }

    static function forget($k)
    {
        unset($_SESSION[$k]);
        unset($_SESSION['_flash.'.$k]);
    }

    static function all()
    {
        return $_SESSION;
    }

    static function get($k)
    {
        if (self::has('_flash.'.$k)) {
            $v = $_SESSION['_flash.'.$k];
            self::forget($k);
        } else {
            $v = $_SESSION[$k];
        }
        return $v;
    }

    static function pull($k)
    {
        $v = self::get($k);
        self::forget($k);
        return $v;
    }
}