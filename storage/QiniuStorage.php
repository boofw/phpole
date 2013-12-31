<?php
require __DIR__.'/../../../qiniu/php-sdk/qiniu/rs.php';
require __DIR__.'/../../../qiniu/php-sdk/qiniu/io.php';

class QiniuStorage
{
	public $ak;
	public $sk;
	public $bucket;
	
	function save($filepath, $savepath)
	{
		Qiniu_SetKeys($this->ak, $this->sk);
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		return Qiniu_PutFile($upToken, $savepath, $filepath, $putExtra);
	}
	
	function getUrl($storageObj)
	{
		return 'http://'.$this->bucket.'.qiniudn.com/'.$storageObj->getPath();
	}
}