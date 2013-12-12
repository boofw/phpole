<?php
class PStorage {
	
	public $name;
	public $dir;
	public $ext;
	public $basepath;
	public $url;
	public $url_m;
	public $url_s;
	
	private function __construct() {
		$this->basepath = PCfg::$cfg['uploadFilePath'].'/s';
	}
	
	static function init($name, $dirPre=NULL) {
		$o = new PStorage();
		$o->name = $name;
		$o->dir = '/'.self::mkDirStr($o->name);
		if ($dirPre) $o->dir = $dirPre.$o->dir;
		$o->ext = pathinfo($o->name, PATHINFO_EXTENSION);
		$o->thumbUrl();
		if (!file_exists($o->basepath.$o->dir.'/'.$o->name)) {
			$o->cache();
		}
		return $o;
	}
	
	static function upload($fileobj, $dirPre=NULL) {
		$fdata = array('oriname'=>$fileobj['name'], 'type'=>$fileobj['type'], 'size'=>$fileobj['size'], 'error'=>$fileobj['error'], 'dateline'=>$_SERVER['REQUEST_TIME']);
		$o = new PStorage();
		$o->ext = PFile::path2ext($fileobj['name']);
		if (!$o->ext) $o->ext = PFile::mime2ext($fileobj['type']);
		if (in_array($o->ext, array('jpeg','jpe'))) $o->ext = 'jpg';
		if ($o->ext=='bmp') {
			$fileobj = self::img2jpg($fileobj);
			$o->ext = 'jpg';
		}
		$o->name = uniqid();
		if ($o->ext) $o->name .= '.'.$o->ext;
		$o->dir = '/'.self::mkDirStr($o->name);
		if ($dirPre) $o->dir = $dirPre.$o->dir;
		$o->thumbUrl();
		PUtil::mkdir($o->basepath.$o->dir);
		move_uploaded_file($fileobj['tmp_name'], $o->basepath.$o->dir.'/'.$o->name);
		$o->save();
		$fdata['name'] = $o->name;
		D('base.storage')->add($fdata);
		return $o;
	}
	
	/**
	 * 返回上传文件访问地址
	 * @param $name
	 * @param $tpl 图片尺寸 格式为 {宽度}x{高度}[x{模式(c:剪切,p:补白)}] 如 100x75 120x120xc 200x200xp
	 */
	static function url($name, $tpl='440x0') {
		$o = self::init($name);
		if (preg_match('/^\d+x\d+(xc|xp)?$/', $tpl) && in_array($o->ext, array('jpg','png','gif'))) {
			return PCfg::$cfg['imgCdnUrl'].'/i'.$o->dir.'/'.$tpl.'/'.$o->name;
		}
		return $o->url;
	}
	
	static function mkDirStr($name) {
		return substr(md5($name.'f7c298fd1c'), 0, 3);
	}
	
	static function img2jpg($fileobj) {
		$pathinfo = pathinfo($fileobj['name']);
		if ($pathinfo['extension']=='bmp') {
			$gd = imagecreatefromwbmp($fileobj['tmp_name']);
			imagejpeg($gd, $fileobj['tmp_name']);
			$fileobj['name'] = $pathinfo['filename'].'.jpg';
			$fileobj['type'] = 'image/jpeg';
		} elseif ($pathinfo['extension']!='jpg') {
			PThumb::init($fileobj['tmp_name'])->save($fileobj['tmp_name'], 'jpg');
			$fileobj['name'] = $pathinfo['filename'].'.jpg';
			$fileobj['type'] = 'image/jpeg';
		}
		return $fileobj;
	}
	
	protected function thumbUrl() {
		$this->url = PCfg::$cfg['imgCdnUrl'].'/upload/s'.$this->dir.'/'.$this->name;
		if (in_array($this->ext, array('jpg','png','gif'))) {
			$this->url_m = PCfg::$cfg['imgCdnUrl'].'/i'.$this->dir.'/440x0/'.$this->name;
			$this->url_s = PCfg::$cfg['imgCdnUrl'].'/i'.$this->dir.'/120x120/'.$this->name;
		}
	}
	
	function save($bcsLocalFilePath=NULL) {
		if (!PCfg::$cfg['baiduBCS']['bucket']) return NULL;
		$bcsObj = $this->dir.'/'.$this->name;
		if (!$bcsLocalFilePath) $bcsLocalFilePath = $this->basepath.$bcsObj;
		if (file_exists($bcsLocalFilePath)) {
			$baiduBCS = PBaiduBCS::init();
			$response = $baiduBCS->create_object(PCfg::$cfg['baiduBCS']['bucket'], $bcsObj, $bcsLocalFilePath);
			return $response->isOK();
		}
		return FALSE;
	}
	
	function cache($bcsLocalFilePath=NULL) {
		if (!PCfg::$cfg['baiduBCS']['bucket']) return NULL;
		$bcsObj = $this->dir.'/'.$this->name;
		if (!$bcsLocalFilePath) $bcsLocalFilePath = $this->basepath.$bcsObj;
		PUtil::mkdir(dirname($bcsLocalFilePath));
		$baiduBCS = PBaiduBCS::init();
		$response = $baiduBCS->get_object (PCfg::$cfg['baiduBCS']['bucket'], $bcsObj, array('fileWriteTo'=>$bcsLocalFilePath));
		return $response->isOK();
	}

}