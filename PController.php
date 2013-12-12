<?php
class PController {
	
	protected $theme = 'default';
	protected $layout = 'main';
	protected $refer;
	protected $ajax;
	
	protected $pgTitle = 'WebSite powered by polev/phpole';
	protected $pgKeywords = '';
	protected $pgDescription = '';
	protected $lv = array(); // layout vars

	function __construct() {
		($this->refer=$_POST['refer']) || ($this->refer=urldecode($_GET['refer'])) || ($this->refer=$_SERVER['HTTP_REFERER']) || ($this->refer='/');
		($this->ajax=isset($_POST['ajax'])) || ($this->ajax=isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest'));
		if (PCfg::$cfg['sitename']) $this->pgTitle = PCfg::$cfg['sitename'];
		if (!$this->theme || !is_dir(PMVC::$approot.'/theme/'.$this->theme)) $this->theme = 'default';
		$this->before();
	}
	
	function __destruct() {
		$this->after();
	}
	
	function __call($func, $args) {
		if (substr($func, 0, 6)=='action') var_dump('404');
		else throw new Exception('Method '.__CLASS__.'::'.$func.' not found!');
	}
	
	function before() {}
	
	function after() {}
	
	function setTitle($s) {
		if ($s) $this->pgTitle = $s . ' - ' . $this->pgTitle;
	}
	
	function redirect($s=NULL, $code=NULL) {
		if (!$s) $s = $this->refer;
		if ($code==301) header('HTTP/1.1 301 Moved Permanently');
		header('location: '.$s);
		exit();
	}
	
	function addError($a, $column, $form) {
		if ($this->ajax) die(json_encode($a));
		else PMVC::$e[$form][$column] = $a;
	}
	
	/**
	 * 消息展示
	 * @param int $status 状态 {0:失败, 1:提示, 2:成功}
	 * @param string $msg
	 * @param array $urls
	 * @param int $time
	 * @param array $data
	 * @param bool $ajax
	 */
	function cmsg($status, $msg, $urls = null, $time = 0, $data = array(), $ajax = null) {
		if (is_null($ajax)) $ajax = $this->ajax;
		if (is_null($urls)) $urls = array(array('link'=>$this->refer, 'title'=>'返回上一页'));
		if (!is_array($urls)) $urls = array();
		if ($urls['link']) $urls = array($urls);
		$urls[] = array('link'=>'/', 'title'=>'返回首页');
		$data['MSG_STATUS'] = $status;
		$data['MSG_MESSAGE'] = $msg;
		$data['MSG_URLS'] = $urls;
		$data['MSG_TIME'] = $time;
		if ($ajax) {
			die(json_encode($data));
		} else {
			$this->layout = 'auth';
			$this->render('cmsg', $data);
		}
	}
	
	protected function show($data=array(), $return=0) {
		$this->render(PMVC::$r['c'].'/'.PMVC::$r['a'], $data, $return);
	}
	
	protected function render($view='', $data=array(), $return=0) {
		$content = $this->renderPartial($view, $data);
		$data = array('content'=>$content);
		$f = $this->getLayoutFile($this->layout);
		$s = $this->renderFile($f, $data);
		$assets = '';
		PMVC::$css = array_unique(PMVC::$css);
		PMVC::$js = array_unique(PMVC::$js);
		foreach (PMVC::$css as $v) {
			if ($v) $assets .= '<link rel="stylesheet" href="'.PCfg::$cfg['assetsUrl'].'/css/'.$v.'.css"/>'."\n";
		}
		$cdnUrl = PCfg::$cfg['imgCdnUrl'] ? PCfg::$cfg['imgCdnUrl'] : PCfg::$cfg['uploadFileUrl'];
		$assets .= "<script>var cdnUrl='$cdnUrl';</script>\n";
		foreach (PMVC::$js as $v) {
			if ($v) $assets .= '<script src="'.PCfg::$cfg['assetsUrl'].'/js/'.$v.'.js"></script>'."\n";
		}
		if (stripos($s, '</head>')) $s = str_ireplace('</head>', $assets.'</head>', $s);
		if ($return) return $s;
		else exit($s);
	}
	
	protected function renderPartial($view='', $data=array()) {
		$f = $this->getViewFile($view);
		return $this->renderFile($f, $data);
	}
	
	protected function lib($s) {
		include $this->getViewFile($s, 'lib');
	}
	
	private function renderFile($file, $data=array()) {
		foreach ($data as $k=>$v) $$k=$v;
		ob_start();
		include $file;
		$s = ob_get_contents();
		ob_clean();
		return $s;
	}
	
	private function getViewFile($view, $type='v') {
		$r = PMVC::$approot.'/theme/'.$this->theme.'/'.$type.'/'.$view.'.php';
		if (!file_exists($r)) $r = PMVC::$approot.'/theme/default/'.$type.'/'.$view.'.php';
		return $r;
	}
	
	private function getLayoutFile($layout) {
		return $this->getViewFile($layout, 'l');
	}
	
}