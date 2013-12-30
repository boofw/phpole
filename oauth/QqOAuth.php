<?php
class QqOAuth extends POAuth
{
	function getLoginUrl($callbackurl) {
		$data = array(
				'response_type'=>'code', 'client_id'=>$this->appid, 'redirect_uri'=>$callbackurl,
				'scope'=>'get_user_info,add_t,add_pic_t,add_share,add_idol',
		);
		return 'https://graph.qq.com/oauth2.0/authorize?'.http_build_query($data);
	}

	function getUserInfo($request = null) {
		if (!$request) $request = $_REQUEST;
		$data = array(
				'grant_type'=>'authorization_code', 'client_id'=>$this->appid, 'client_secret'=>$this->key,
				'code'=>$request['code'], 'redirect_uri'=>$request['callbackurl'],
		);
		$s = PHttp::get('https://graph.qq.com/oauth2.0/token', $data);
		parse_str($s, $r);
		$this->access_token = $r['access_token'];
		$rinfo = PHttp::get('https://graph.qq.com/oauth2.0/me?access_token='.$this->access_token);
		$rinfo = trim($rinfo, "callback();\r\n ");
		$rinfo = json_decode($rinfo, true);
		$this->openid = $rinfo['openid'];
		$rinfo = PHttp::get('https://graph.qq.com/user/get_user_info', $this->mergeParam(array('format'=>'json')));
		$rinfo = json_decode($rinfo, true);
		return array(
				'access_token'=>$this->access_token, 'expires_in'=>$r['expires_in'],
				'uid'=>$this->openid, 'name'=>$rinfo['nickname'], 'avatar'=>$rinfo['figureurl_2'],
				'refresh_token'=>''
		);
	}

	function add_t($content, $img = null, $pos = array(), $args = array()) {
		if ($args['title']) $this->add_share($content, $img, $args);
		$url = 'https://graph.qq.com/t/add_t';
		$data = array(
			'format' => 'json',
			'content' => $content,
			'clientip' => $pos['ip'],
			'jing' => $pos['jing'],
			'wei' => $pos['wei'],
		);
		if ($img) {
			$file = __DIR__.'/../runtime/'.time().uniqid().trim(strrchr($img, '/'), '/ ');
			copy($img, $file);
			$data['pic'] = '@'.$file;
			$url = 'https://graph.qq.com/t/add_pic_t';
		}
		$data = $this->mergeParam($data);
		$r = $this->post($url, $data);
		$r = json_decode($r, 1);
		return $r['data']['id'];
	}
	
	private function add_share($content, $img = null, $args = array()) {
		$url = 'https://graph.qq.com/share/add_share';
		$data = array(
				'title' => $args['title'],
				'url' => 'http://'.PCfg::$cfg['basedomain'].'/'.$args['id'],
				'summary' => $args['meta'],
				'images' => AUtil::imgUrl($args['iid'], '120x120'),
				'site' => PCfg::$cfg['sitename'],
				'fromurl' => 'http://'.PCfg::$cfg['basedomain'],
				'nswb' => 1,
		);
		$data = $this->mergeParam($data);
		$ret = $this->post($url, $data);
		return $ret;
	}
	
	function follow($rid) {
		$url = 'https://graph.qq.com/relation/add_idol';
		$data = array(
				'format' => 'json',
				'fopenids' => $rid,
		);
		$data = $this->mergeParam($data);
		$ret = $this->post($url, $data);
		return $ret;
	}

	private function mergeParam($data) {
		$param = array(
				'access_token' => $this->access_token,
				'oauth_consumer_key' => $this->appid,
				'openid' => $this->openid,
		);
		if (is_array($data)) $param = array_merge($data, $param);
		return $param;
	}

}