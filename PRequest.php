<?php
class PRequest
{
    static $ajax = null;
    static $refer = null;
    static $ip = null;
    static $domain = null;

    static function ajax()
    {
        if (is_null(self::$ajax)) {
            self::$ajax = (isset($_POST['ajax']) || isset($_GET['ajax']) || PArray::get($_SERVER, 'HTTP_X_REQUESTED_WITH')==='XMLHttpRequest');
        }
        return self::$ajax;
    }

    static function refer()
    {
        if (is_null(self::$refer)) {
            (self::$refer = $_POST['refer']) || (self::$refer = urldecode($_GET['refer'])) || (self::$refer = $_SERVER['HTTP_REFERER']) || (self::$refer = '/');
        }
        return self::$refer;
    }

    static function ip()
    {
        if (is_null(self::$ip)) {
            self::$ip = PArray::get($_SERVER, 'HTTP_X_REAL_IP') ?: PArray::get($_SERVER, 'REMOTE_ADDR');
        } else {
            self::$ip = '';
        }
        return self::$ip;
    }

    static function domain()
    {
        if (is_null(self::$domain)) {
            self::$domain = PArray::get($_SERVER, 'HTTP_X_REAL_HOST') ?: PArray::get($_SERVER, 'HTTP_HOST');
        } else {
            self::$domain = '';
        }
        return self::$domain;
    }
}