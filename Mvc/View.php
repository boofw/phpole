<?php namespace Polev\Phpole\Mvc;

class View
{
    static $viewDir = '';
    static $themeDir = '';

    static $theme = '';
    static $layout = '';

    static function theme($theme = null)
    {
        if ($theme) {
            self::$theme = $theme;
        }
        if (!is_dir(self::$themeDir.'/'.self::$theme)) {
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
        $viewFilePath = self::$viewDir.'/'.$view.'.php';
        if (self::$theme) {
            $themeFilePath = self::$themeDir.'/'.self::$theme.'/'.$view.'.php';
            if (file_exists($themeFilePath)) {
                $viewFilePath = $themeFilePath;
            }
        }
        return $viewFilePath;
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