<?php
class PAvatar extends PStorage
{
	protected $avatarBucketFix = '-avatar';
	
	static function init($uid)
	{
		$o = new self();
		$o->name = $uid;
		$o->ext = 'jpg';
		$o->dir = $o->mkDirHash();
		return $o;
	}

	static function upload($fileobj, $uid=NULL)
	{
		if (!$uid) {
			$uid = $_SESSION['user']['id'];
		}
		$o = self::init($uid);
		$o->saveFile($fileobj['tmp_name']);
		return $o;
	}

	protected function mkDirHash()
	{
		$lid = str_pad($this->name, 9, '0', STR_PAD_LEFT);
		$dir = substr($lid, 0, 3).'/'.substr($lid, 3, 3);
		if (!$this->avatarBucketFix) {
			$dir = 'avatar/'.$dir;
		}
		return $dir;
	}
}