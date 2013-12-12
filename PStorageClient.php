<?php
class PStorageClient
{
	public $_appId;
	public $_appKey;

	static function init($AppId=NULL, $AppKey=NULL) {
		if (!$AppId || !$AppKey) {
			$AppId = BASESVC_APPID;
			$AppKey = BASESVC_APPKEY;
		}
		return new self($AppId, $AppKey);
	}

	private function __construct($AppId, $AppKey) {
		$this->_appId = $AppId;
		$this->_appKey = $AppKey;
	}

	function save($fileObj) {
		if (!$fileObj['tmp_name']) return array('errormessage'=>'上传文件失败，请检查上传的文件是否符合要求');

		$postdata = array(
				'appid' => $this->_appId,
				'filejson' => json_encode($fileObj),
				'FileByAPI' => '@'.$fileObj['tmp_name'],
				'token' => sha1($fileObj['tmp_name'].'%|#'.$this->_appId.'*|^'.$this->_appKey.'C|D|StorageClient')
		);

		$ch = curl_init(rtrim(BASESVC_URL, '/').'/upload');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($ch);
		curl_close($ch);

		//print_r($res);

		return json_decode($res, true);
	}
	
	static function upload($fileObj, $AppId=NULL, $AppKey=NULL) {
		return self::init($AppId, $AppKey)->save($fileObj);
	}
}