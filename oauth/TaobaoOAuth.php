<?php
class TaobaoOAuth extends POAuth
{
	function getLoginUrl($callbackurl=NULL) {
		if (!$callbackurl) {
			$callbackurl = self::$callback;
		}
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>$callbackurl,
		);
		return 'https://oauth.taobao.com/authorize?'.http_build_query($data);
	}

	function getUserInfo($request = null) {
		if (!$request) $request = $_REQUEST;
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->key,
				'code'=>$request['code'], 'redirect_uri'=>$request['callbackurl'],
		);
		$r = PHttp::post('https://oauth.taobao.com/token', $data);
		$r = json_decode($r, 1);
		$this->access_token = $r['access_token'];
		$this->openid = $r['taobao_user_id'];
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$r['taobao_user_nick'], 'avatar'=>'',
				'refresh_token'=>$r['refresh_token']
		);
	}

	function add_t($content, $img = null, $ip = null, $jing = null, $wei = null) {

	}

}