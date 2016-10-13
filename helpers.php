<?php

if ( ! function_exists('query_url'))
{
    function query_url($parameters, $path = null)
    {
        if (is_null($path)) {
            $url = $_SERVER['REQUEST_URI'];
        } else {
            $url = url($path);
        }

        $ux = parse_url($url);
        parse_str(array_get($ux, 'query'), $uargs);

        foreach ($uargs as $k => $v) {
            if (starts_with($k, '/')) {
                unset($uargs[$k]);
            }
        }

        if (isset($uargs['page'])) {
            unset($uargs['page']);
        }

        $uargs = array_merge($uargs, $parameters);

        if (array_get($uargs, 'page') == 1) {
            unset($uargs['page']);
        }

        foreach ($parameters as $k => $v) {
            if (is_null($v)) {
                unset($uargs[$k]);
            }
        }

        $ux['query'] = http_build_query($uargs);

        $r = '';

        if (array_get($ux, 'scheme') && array_get($ux, 'host')) {
            $r .= $ux['scheme'] . '://' . $ux['host'];
        }

        if (array_get($ux, 'path')) {
            $r .= $ux['path'];
        }

        if (array_get($ux, 'query')) {
            $r .= '?' . $ux['query'];
        }

        if (array_get($ux, 'fragment')) {
            $r .= '#'.$ux['fragment'];
        }
        return $r;
    }
}

if ( ! function_exists('date_to_timestamp'))
{
    function date_to_timestamp($date)
    {
        if (is_numeric($date)) {
            return $date;
        }
        return strtotime($date);
    }
}

if ( ! function_exists('date_for_humans'))
{
    function date_for_humans($time, $now = null)
    {
        $time = date_to_timestamp($time);
        if ($now) {
            $now = date_to_timestamp($now);
            $now = \Carbon\Carbon::createFromTimestamp($now);
        }
        $r = \Carbon\Carbon::createFromTimestamp($time)->diffForHumans($now);
        $r = str_replace(' seconds', '秒', $r);
        $r = str_replace(' minutes', '分钟', $r);
        $r = str_replace(' hours', '小时', $r);
        $r = str_replace(' days', '天', $r);
        $r = str_replace(' weeks', '周', $r);
        $r = str_replace(' months', '个月', $r);
        $r = str_replace(' years', '年', $r);
        $r = str_replace(' second', '秒', $r);
        $r = str_replace(' minute', '分钟', $r);
        $r = str_replace(' hour', '小时', $r);
        $r = str_replace(' day', '天', $r);
        $r = str_replace(' week', '周', $r);
        $r = str_replace(' month', '个月', $r);
        $r = str_replace(' year', '年', $r);
        $r = str_replace(' from now', '后', $r);
        $r = str_replace(' ago', '前', $r);
        $r = str_replace(' after', '后', $r);
        $r = str_replace(' before', '前', $r);
        return $r;
    }
}

if ( ! function_exists('pager_links'))
{
    function pager_links($data, $linksCount = 10, $pageKey = 'page')
    {
        if ($data['page'] < 1) {
            $data['page'] = 1;
        }
        $half = floor(($linksCount - 1) / 2);
        $min = $data['page'] - $half;
        if ($min < 1) {
            $min = 1;
        }
        $max = $min + $linksCount;
        if ($max > $data['pagemax'] + 1) {
            $max = $data['pagemax'] + 1;
        }
        $min = $max - $linksCount;
        if ($min < 1) {
            $min=1;
        }

        $s = '';
        if ($data['page']>1) {
            $s .= '<li><a href="'.query_url(['page'=>1]).'">&laquo;</a></li>';
            $s .= '<li><a href="'.query_url(['page'=>$data['page']-1]).'">&lsaquo;</a></li>';
        } else {
            $s .= '<li class="disabled"><a href="'.query_url(['page'=>1]).'">&laquo;</a></li>';
            $s .= '<li class="disabled"><a href="'.query_url(['page'=>1]).'">&lsaquo;</a></li>';
        }
        for ($i = $min; $i < $max; $i++) {
            $onstat = '';
            if($i == $data['page']) {
                $onstat = ' class="active"';
            }
            $s .= '<li'.$onstat.'><a href="'.query_url(['page'=>$i]).'">'.$i.'</a></li>';
        }
        if ($data['page'] < $data['pagemax']) {
            $s .= '<li><a href="'.query_url(['page'=>$data['page']+1]).'">&rsaquo;</a></li>';
            $s .= '<li><a href="'.query_url(['page'=>$data['pagemax']]).'">&raquo;</a></li>';
        } else {
            $s .= '<li class="disabled"><a href="'.query_url(['page'=>$data['page']+1]).'">&rsaquo;</a></li>';
            $s .= '<li class="disabled"><a href="'.query_url(['page'=>$data['pagemax']]).'">&raquo;</a></li>';
        }
        return '<ul class="pagination">'.$s.'</ul>';
    }
}

if ( ! function_exists('xml_to_array'))
{
    function xml_to_array($data)
    {
        $data = simplexml_load_string($data, null, LIBXML_NOCDATA);
        $data = json_decode(json_encode($data), 1);
        if ( ! is_array($data)) $data = [];
        return $data;
    }
}

if ( ! function_exists('array_to_xml'))
{
    function array_to_xml($data)
    {
        $r = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $r .= "<$k>" . array_to_xml($v) . "</$k>";
            } elseif (is_string($v)) {
                $r .= "<$k><![CDATA[$v]]></$k>";
            } else {
                $r .= "<$k>$v</$k>";
            }
        }
        return $r;
    }
}

if ( ! function_exists('view_extend'))
{
    function view_extend($layout)
    {
        \Boofw\Phpole\Mvc\View::extend($layout);
    }
}
if ( ! function_exists('view_insert'))
{
    function view_insert($view, $data = [])
    {
        \Boofw\Phpole\Mvc\View::insert($view, $data);
    }
}

if ( ! function_exists('session'))
{
    function session($k, $default = '')
    {
        if (is_array($k)) {
            foreach ($k as $sk => $sv) {
                \Boofw\Phpole\Http\Session::put($sk, $sv);
            }
            return null;
        }
        return \Boofw\Phpole\Http\Session::get($k, $default);
    }
}

if ( ! function_exists('cache'))
{
    function cache($k, $default = '')
    {
        return \Boofw\Phpole\Cache\Cache::get($k, $default);
    }
}

if ( ! function_exists('config'))
{
    function config($k, $default = '')
    {
        if (is_array($k)) {
            foreach ($k as $sk => $sv) {
                \Boofw\Phpole\App\Config::set($sk, $sv);
            }
            return null;
        }
        return \Boofw\Phpole\App\Config::get($k, $default);
    }
}

if ( ! function_exists('runtime'))
{
    function runtime($k, $default = '')
    {
        $newKey = [];
        if (is_array($k)) {
            foreach ($k as $sk => $sv) {
                $newKey['runtime.'.$sk] = $sv;
            }
            return config($newKey, $default);
        }
        return config('runtime.'.$k, $default);
    }
}

if ( ! function_exists('runtime_set'))
{
    function runtime_set($k, $v)
    {
        return \Boofw\Phpole\App\Config::set('runtime.'.$k, $v);
    }
}

if ( ! function_exists('input'))
{
    function input($k, $default = '')
    {
        return \Boofw\Phpole\Http\Input::get($k, $default);
    }
}

if ( ! function_exists('old'))
{
    function old($k, $default = '')
    {
        return \Boofw\Phpole\Http\Input::old($k, $default);
    }
}

if ( ! function_exists('view'))
{
    function view($data = [], $view = null)
    {
        if ($view) return \Boofw\Phpole\Mvc\View::render($view, $data);
        return \Boofw\Phpole\Mvc\View::show($data);
    }
}

if ( ! function_exists('success'))
{
    function success($message, $uri = null, $withInput = false, $data = [])
    {
        return \Boofw\Phpole\Http\Response::success($message, $uri, $withInput, $data);
    }
}

if ( ! function_exists('error'))
{
    function error($message, $uri = null, $withInput = false, $data = [])
    {
        return \Boofw\Phpole\Http\Response::error($message, $uri, $withInput, $data);
    }
}

if ( ! function_exists('array_reset_key'))
{
    function array_reset_key($array, $column)
    {
        return \Boofw\Phpole\Helper\Arr::resetKey($array, $column);
    }
}

if ( ! function_exists('first'))
{
    function first($array)
    {
        list($r,) = array_values($array);
        return $r;
    }
}

if ( ! function_exists('url'))
{
    function url($uri = '', $withDomain = 0)
    {
        $appPath = config('app.path', dirname($_SERVER['PHP_SELF']));
        $url = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $appPath), '/').'/'.ltrim($uri, '/');
        if ($withDomain) $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
        return $url;
    }
}

if ( ! function_exists('assets'))
{
    function assets($uri = '', $withDomain = 0)
    {
        $appPath = config('app.assets', dirname($_SERVER['PHP_SELF']));
        $url = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $appPath), '/').'/'.ltrim($uri, '/');
        if ($withDomain) $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
        return $url;
    }
}

if ( ! function_exists('route'))
{
    function route($k = 'route')
    {
        if ($k === 'controller') {
            return \Boofw\Phpole\Mvc\Route::$controller;
        } elseif ($k === 'action') {
            return \Boofw\Phpole\Mvc\Route::$action;
        } else {
            return \Boofw\Phpole\Mvc\Route::$route;
        }
    }
}

if ( ! function_exists('str_tidy'))
{
    function str_tidy($s, $len = 0)
    {
        $s = strip_tags($s);
        $s = strip_tags(html_entity_decode($s));
        $s = str_replace(array('　',"\n","\r","\t"), ' ', $s);
        $s = preg_replace('/\s{2,}/', ' ', $s);
        $s = trim($s);
        if ($len) $s = mb_substr($s, 0, $len, 'utf-8');
        return $s;
    }
}

if ( ! function_exists('str_tidy_with_br'))
{
    function str_tidy_with_br($s, $len = 0) {
        $s = nl2br($s);
        $s = preg_replace('/\s{2,}/', ' ', $s);
        $s = str_replace('<p', "\n<p", $s);
        $s = str_replace('<div', "\n<div", $s);
        $s = str_replace('<br', "\n<br", $s);
        $s = str_replace('<li', "\n<li", $s);
        $s = strip_tags($s);
        $s = strip_tags(html_entity_decode($s));
        $s = str_replace("\r", "\n", $s);
        $s = preg_replace('/\n{2,}/', "\n", $s);
        $s = trim($s);
        if ($len) $s = mb_substr($s, 0, $len, 'utf-8');
        $s = nl2br($s);
        return $s;
    }
}

if ( ! function_exists('auth'))
{
    function auth($k = '')
    {
        $user = \Boofw\Phpole\App\Auth::user();
        if ($k) {
            return array_get($user, $k);
        }
        return $user;
    }
}

if (!function_exists('curl_file_create'))
{
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename=".($postname ?: basename($filename)).($mimetype ? ";type=$mimetype" : '');
    }
}
