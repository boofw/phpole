<?php
class PAvatar extends PStorage {
	
	public $id;
	
	private function __construct() {
		$this->ext = 'jpg';
		$this->basepath = PCfg::$cfg['uploadFilePath'];
	}
	
	static function init($uid) {
		$o = new PAvatar();
		$o->id = $uid;
		$o->name = $o->id.'.'.$o->ext;
		$o->dir = '/u/'.self::mkDirStr($o->name);
		$o->url = PCfg::$cfg['imgCdnUrl'].'/upload'.$o->dir.'/'.$o->name;
		if (!file_exists($o->basepath.$o->dir.'/'.$o->name)) {
			$o->cache();
		}
		return $o;
	}
	
	static function upload($fileobj, $uid) {
		$o = new PAvatar();
		$o->id = $uid;
		$o->name = $o->id.'.'.$o->ext;
		$o->dir = '/u/'.self::mkDirStr($o->name);
		$o->url = PCfg::$cfg['imgCdnUrl'].'/upload'.$o->dir.'/'.$o->name;
		$fileobj = self::img2jpg($fileobj);
		PUtil::mkdir($o->basepath.$o->dir);
		move_uploaded_file($fileobj['tmp_name'], $o->basepath.$o->dir.'/'.$o->name);
		$o->save();
		return $o;
	}
	
	/**
	 * 头像文件访问地址
	 * @param $name
	 * @param $width 宽度
	 */
	static function url($uid, $width=50) {
		$o = self::init($uid);
		$width = intval($width);
		if ($width) {
			return PCfg::$cfg['imgCdnUrl'].$o->dir.'/'.$width.'/'.$o->name;
		}
		return $o->url;
	}

}