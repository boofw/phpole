<?php namespace Boofw\Phpole\Mvc;

class View
{
    static $viewDir = '';
    static $themeDir = '';
    static $customGetViewFileFunc = [];

    static $theme = '';
    static $layout = '';

    static function theme($theme = null)
    {
        if ( ! self::$themeDir) {
            self::$themeDir = dirname(dirname(dirname(dirname(__DIR__)))).'/theme';
        }

        if ($theme) {
            self::$theme = $theme;
        }
        if ( ! is_dir(self::$themeDir.'/'.self::$theme)) {
            self::$theme = '';
        }
        return self::$theme;
    }

    static function extend($layout)
    {
        self::$layout = $layout;
    }

    static function insert($view)
    {
        include self::getViewFile($view);
    }

    static function getViewFile($view)
    {
        if ( ! self::$viewDir) {
            self::$viewDir = dirname(dirname(dirname(dirname(__DIR__)))).'/view';
        }
        if ( ! self::$themeDir) {
            self::$themeDir = dirname(dirname(dirname(dirname(__DIR__)))).'/theme';
        }

        $viewFilePath = self::$viewDir.'/'.$view.'.php';
        if (self::$theme) {
            $themeFilePath = self::$themeDir.'/'.self::$theme.'/'.$view.'.php';
            if (file_exists($themeFilePath)) {
                $viewFilePath = $themeFilePath;
            }
        }
        if (file_exists($viewFilePath)) {
            return $viewFilePath;
        }
        if (isset(self::$customGetViewFileFunc[1]) && method_exists(self::$customGetViewFileFunc[0], self::$customGetViewFileFunc[1])) {
            return call_user_func_array([self::$customGetViewFileFunc[0], self::$customGetViewFileFunc[1]], [$view]);
        }
        return null;
    }

    static function renderFile($file, $data = [])
    {
        foreach ($data as $k=>$v) {
            $$k=$v;
        }
        ob_start();
        include $file;
        $s = ob_get_contents();
        ob_clean();
        return $s;
    }

    static function render($view, $data = [])
    {
        $content = self::renderFile(self::getViewFile($view), $data);
        if (self::$layout) {
            $data['content'] = $content;
            $content = self::renderFile(self::getViewFile(self::$layout), $data);
        }
        return $content;
    }

    static function show($data = [])
    {
        return self::render(Route::$controller.'/'.Route::$action, $data);
    }
}