<?php
class SinaOAuth extends POAuth
{	
	function getLoginUrl($callbackurl) {
		$o = new SaeTOAuthV2($this->appid, $this->key);
		return $o->getAuthorizeURL($callbackurl);
	}
	
	function getUserInfo($request = null) {
		if (!$request) $request = $_REQUEST;
		$o = new SaeTOAuthV2($this->appid, $this->key);
		$r = $o->getAccessToken('code', array('code'=>$request['code'], 'redirect_uri'=>$request['callbackurl']));
		$this->access_token = $r['access_token'];
		$c = new SaeTClientV2($this->appid, $this->key, $r['access_token']);
		$user = $c->get_uid();
		$this->openid = $user['uid'];
		$rinfo = $c->show_user_by_id($user['uid']);
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$rinfo['name'], 'avatar'=>$rinfo['avatar_large'], 
				'refresh_token'=>''
		);
	}
	
	function add_t($content, $img = null, $pos = array(), $args = array()) {
		$c = new SaeTClientV2($this->appid, $this->key, $this->access_token);
		if ($img) $r = $c->upload($content, $img, $pos['wei'], $pos['jing']);
		else $r = $c->update($content, $pos['wei'], $pos['jing']);
		return $r['idstr'];
	}
	
	function follow($rid) {
		$c = new SaeTClientV2($this->appid, $this->key, $this->access_token);
		return $c->follow_by_id($rid);
	}

}