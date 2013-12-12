<?php
class PHttp {
	
	static function get($url, $data=null) {
		if ($data) {
			$data = http_build_query($data);
			$url = trim($url, '?& ');
			if (strpos($url, '?')) $url .= '&'.$data;
			else $url .= '?'.$data;
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$ret =  curl_exec($ch);
		curl_close($ch);
		return $ret;
	}

	static function post($url, $data) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
	
	/**
	 * 下载远程附件
	 * @param string $url
	 * @param string $ext
	 * @param string $filename
	 * @param string $directory
	 * @param int $chmod
	 * @param string $referer
	 * @return array {name:原始文件名, ext:扩展名, fullpath:全路径}
	 */
	public static function download($url, $ext = NULL, $filename = NULL, $directory = NULL, $chmod = 0644, $referer = NULL) {
		if (!$referer) {
			$referer = $url;
		}
		$ch = curl_init($url);
		//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.52.7');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt( $ch, CURLOPT_AUTOREFERER, 1 );
		//curl_setopt( $ch, CURLOPT_NOBODY, 1 );
		$ret = curl_exec($ch);
		$chinfo = curl_getinfo( $ch );
		while ($chinfo['redirect_url']) {
			curl_setopt( $ch, CURLOPT_URL, $chinfo['redirect_url'] );
			$ret = curl_exec( $ch );
			$chinfo = curl_getinfo( $ch );
		}
		curl_close($ch);
	
		if (!$ext) {
			if (!self::$mimeTypes) self::$mimeTypes = require(Yii::getPathOfAlias('system.utils.mimeTypes').'.php');
			if (in_array($chinfo['content_type'], self::$mimeTypes)) $ext = self::$mimeTypes[$chinfo['content_type']];
			else $ext = substr($chinfo['url'], strrpos($chinfo['url'], '.')+1);
		}
		$ext = strtolower($ext);
	
		$ori_name = urldecode(substr($chinfo['url'], strrpos($chinfo['url'], '/')+1, 0-(strlen(strrchr($chinfo['url'], '.')))));
		if (!$filename) $filename = time().substr(md5($ori_name), 0, 8);
	
		$filename = realpath($directory).DIRECTORY_SEPARATOR.$filename.'.'.$ext;
		file_put_contents($filename, $ret);
		if ($chmod !== FALSE) chmod($filename, $chmod);
	
		return array(
				'name' => $ori_name,
				'ext' => $ext,
				'fullpath' => $filename,
		);
	}
}