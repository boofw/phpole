<?php
class SinaOAuth extends POAuth
{	
	function getLoginUrl()
	{
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>self::$callback,
				//'scope'=>'all',
		);
		return 'https://api.weibo.com/oauth2/authorize?'.http_build_query($data);
	}
	
	function getUserInfo($code)
	{
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->appkey,
				'code'=>$code, 'redirect_uri'=>self::$callback,
		);
		$r = PHttp::post('https://api.weibo.com/oauth2/access_token', $data);
		$r = json_decode($r, 1);
		$this->access_token = $r['access_token'];
		$this->openid = $r['uid'];
		$rinfo = PHttp::get('https://api.weibo.com/2/users/show.json', array('access_token'=>$this->access_token, 'uid'=>$this->openid));
		$rinfo = json_decode($rinfo, 1);
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$rinfo['screen_name'], 'avatar'=>$rinfo['avatar_large'],
				'refresh_token'=>''
		);
	}

	function add_t($content, $imgpath = null, $pos = array(), $args = array())
	{
		$data = array(
				'access_token'=>$this->access_token,
				'status'=>urlencode($content),
		);
		$url = 'https://api.weibo.com/2/statuses/update.json';
		if ($imgpath && file_exists($imgpath)) {
			$url = 'https://upload.api.weibo.com/2/statuses/upload.json';
			$data['pic'] = '@'.$imgpath;
		}
		return PHttp::post($url, $data);
	}
	
	function follow($rid) {
		return PHttp::post('https://api.weibo.com/2/friendships/create.json', array(
				'access_token'=>$this->access_token,
				'uid'=>$rid,
		));
	}
}