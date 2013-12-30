<?php
class BaiduOAuth extends POAuth
{
	function getLoginUrl($callbackurl) {
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>$callbackurl,
		);
		return 'https://openapi.baidu.com/oauth/2.0/authorize?'.http_build_query($data);
	}

	function getUserInfo($request = null) {
		if (!$request) $request = $_REQUEST;
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->key,
				'code'=>$request['code'], 'redirect_uri'=>$request['callbackurl'],
		);
		$r = PHttp::post('https://openapi.baidu.com/oauth/2.0/token', $data);
		$r = json_decode($r, 1);
		$this->access_token = $r['access_token'];
		$rinfo = PHttp::post('https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser', array('access_token'=>$this->access_token));
		$rinfo = json_decode($rinfo, 1);
		$this->openid = $rinfo['uid'];
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$rinfo['uname'], 'avatar'=>'http://himg.bdimg.com/sys/portrait/item/'.$rinfo['portrait'].'.jpg',
				'refresh_token'=>$r['refresh_token']
		);
	}

	function add_t($content, $img = null, $ip = null, $jing = null, $wei = null) {

	}

}