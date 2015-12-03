<?php
class PView
{
    static $theme = '';
    static $layout = 'main';

    static $pgTitle = null;
    static $pgKeywords = '';
    static $pgDescription = '';

    static function theme($theme=null)
    {
        if ($theme) {
            self::$theme = $theme;
        }
        if (!is_dir(PMVC::$approot.'/theme/'.self::$theme)) {
            self::$theme = '';
        }
        return self::$theme;
    }

    static function title($s='')
    {
        if (self::$pgTitle===null) {
            if (PCfg::$cfg['sitename']) {
                self::$pgTitle = PCfg::$cfg['sitename'];
            } else {
                self::$pgTitle = 'WebSite powered by boofw/phpole';
            }
        }
        if ($s) {
            self::$pgTitle = $s . ' - ' . self::$pgTitle;
        }
        return self::$pgTitle;
    }

    static function show($data=array())
    {
        return self::render(PMVC::$r['c'].'/'.PMVC::$r['a'], $data);
    }

    static function render($view='', $data=array())
    {
        $content = self::renderPartial($view, $data);
        $data = array('content'=>$content);
        $f = self::getLayoutFile(self::$layout);
        $s = self::renderFile($f, $data);
        $assets = '';
        PMVC::$css = array_unique(PMVC::$css);
        PMVC::$js = array_unique(PMVC::$js);
        foreach (PMVC::$css as $v) {
            if ($v) $assets .= '<link rel="stylesheet" href="'.PCfg::$cfg['assetsUrl'].'/css/'.$v.'.css"/>'."\n";
        }
        foreach (PMVC::$js as $v) {
            if ($v) $assets .= '<script src="'.PCfg::$cfg['assetsUrl'].'/js/'.$v.'.js"></script>'."\n";
        }
        if (stripos($s, '</head>')) $s = str_ireplace('</head>', $assets.'</head>', $s);
        return $s;
    }

    static function renderPartial($view='', $data=array())
    {
        $f = self::getViewFile($view);
        return self::renderFile($f, $data);
    }

    static function lib($s)
    {
        include self::getViewFile($s, 'lib');
    }

    static function renderFile($file, $data=array())
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

    static function getViewFile($view, $type='page')
    {
        $r = PMVC::$approot.'/view/'.$type.'/'.$view.'.php';
        if (self::$theme) {
            $r = PMVC::$approot.'/theme/'.self::$theme.'/'.$type.'/'.$view.'.php';
            if (!file_exists($r)) {
                $r = PMVC::$approot.'/view/'.$type.'/'.$view.'.php';
            }
        }
        return $r;
    }

    static function getLayoutFile($layout)
    {
        return self::getViewFile($layout, 'layout');
    }
}