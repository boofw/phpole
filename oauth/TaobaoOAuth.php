<?php
class TaobaoOAuth extends POAuth
{
	function getLoginUrl()
	{
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>self::$callback,
				//'scope'=>'',
		);
		return 'https://oauth.taobao.com/authorize?'.http_build_query($data);
	}

	function getUserInfo($code)
	{
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->appkey,
				'code'=>$code, 'redirect_uri'=>self::$callback,
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
}