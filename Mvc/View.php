<?php namespace Boofw\Phpole\Mvc;

class View
{
    static $viewDir = '';
    static $themeDir = '';
    static $appDir = '';

    static $theme = '';
    static $layout = '';

    static function theme($theme = null)
    {
        if ( ! is_null($theme)) {
            self::$theme = $theme;
        }
        return self::$theme;
    }

    static function extend($layout)
    {
        self::$layout = $layout;
    }

    static function insert($view, $data = [])
    {
        foreach ($data as $k=>$v) {
            $$k=$v;
        }
        include self::getViewFile($view);
    }

    static function getViewFile($view, $viewDir = '', $themeDir = '')
    {
        if ( ! $viewDir) {
            if ( ! self::$viewDir) {
                self::$viewDir = dirname(dirname(dirname(dirname(__DIR__)))).'/view';
            }
            $viewDir = self::$viewDir;
        }
        if ( ! $themeDir) {
            if ( ! self::$themeDir) {
                self::$themeDir = dirname(dirname(dirname(dirname(__DIR__)))).'/theme';
            }
            $themeDir = self::$themeDir;
        }

        $viewFilePath = $viewDir.'/'.$view.'.php';
        if (self::$theme) {
            $themeFilePath = $themeDir.'/'.self::$theme.'/'.$view.'.php';
            if (file_exists($themeFilePath)) {
                $viewFilePath = $themeFilePath;
            }
        }
        if (file_exists($viewFilePath)) {
            return $viewFilePath;
        }

        if (self::$appDir) {
            return self::getViewFile($view, self::$appDir.'/view', self::$appDir.'/theme');
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