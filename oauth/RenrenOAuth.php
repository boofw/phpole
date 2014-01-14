<?php
class RenrenOAuth extends POAuth
{
	function getLoginUrl()
	{
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>self::$callback,
				'scope'=>'publish_feed',
		);
		return 'https://graph.renren.com/oauth/authorize?'.http_build_query($data);
	}

	function getUserInfo($code)
	{
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->appkey,
				'code'=>$code, 'redirect_uri'=>self::$callback,
		);
		$r = PHttp::post('http://graph.renren.com/oauth/token', $data);
		$r = json_decode($r, 1);
		$this->access_token = $r['access_token'];
		$this->openid = $r['user']['id'];
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$r['user']['name'], 'avatar'=>$r['user']['avatar'][3]['url'],
				'refresh_token'=>$r['refresh_token']
		);
	}

	function add_t($content, $imgpath = null, $pos = array(), $args = array())
	{
		$url = 'https://api.renren.com/v2/feed/put';
		$data = array(
				'message' => $content,
				'title' => $args['title'],
				'description' => $content,
				'targetUrl' => 'http://my.wudao.com/feed/'.$args['id'],
				'access_token' => $this->access_token,
		);
		$r = PHttp::post($url, $data);
		return $r;
	}
}