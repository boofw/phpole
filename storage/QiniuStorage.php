<?php
require __DIR__.'/../../../qiniu/php-sdk/qiniu/rs.php';
require __DIR__.'/../../../qiniu/php-sdk/qiniu/io.php';

class QiniuStorage extends PStorage
{
	public $ak;
	public $sk;
	public $bucket;
	
	protected function saveFile($filepath)
	{
		Qiniu_SetKeys($this->ak, $this->sk);
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		return Qiniu_PutFile($upToken, $this->getPath(), $filepath, $putExtra);
	}
	
	protected function getUrl()
	{
		return 'http://'.$this->bucket.'.qiniudn.com/'.$this->getPath();
	}
}