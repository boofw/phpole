<?php
class SinaOAuth extends POAuth
{	
	function getLoginUrl($callbackurl=NULL) {
		$data = array(
				'client_id'=>$this->appid, 'redirect_uri'=>self::$callback,
				//'scope'=>'all',
		);
		return 'https://api.weibo.com/oauth2/authorize?'.http_build_query($data);
	}
	
	function getUserInfo($request=NULL) {
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->appkey,
				'code'=>$request['code'], 'redirect_uri'=>self::$callback,
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
	
	function add_t($content, $img = null, $pos = array(), $args = array()) {
		// @todo add_t
		$c = new SaeTClientV2($this->appid, $this->key, $this->access_token);
		if ($img) $r = $c->upload($content, $img, $pos['wei'], $pos['jing']);
		else $r = $c->update($content, $pos['wei'], $pos['jing']);
		return $r['idstr'];
	}
	
	function follow($rid) {
		// @todo follow
		$c = new SaeTClientV2($this->appid, $this->key, $this->access_token);
		return $c->follow_by_id($rid);
	}

}