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
}