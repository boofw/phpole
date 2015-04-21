<?php
class PAuth
{
	static function user()
	{
		return $_SESSION['user'];
	}

	static function loginReqired($returnUrl=NULL) {
		if (!$_SESSION['user']['id']) {
			if (!$returnUrl) {
				$returnUrl = $_SERVER['REQUEST_URI'];
			}
			$_SESSION['loginrefer'] = $returnUrl;
			return PResponse::redirect('/auth/login');
		} elseif (is_string($returnUrl) && $returnUrl) {
			return PResponse::redirect($returnUrl);
		}
	}

	static function login($user) {
		if (!is_array($user)) $user = array('id'=>intval($user));
		$_SESSION['user'] = $user;
		return self::loginRefer();
	}

	static function loginRefer() {
		($refer=$_SESSION['loginrefer']) || ($refer=PRequest::refer());
		unset($_SESSION['loginrefer']);
		return PResponse::redirect($refer);
	}

	static function setLoginReferUri($uri=NULL, $force=TRUE) {
		if (!$uri) $uri = PRequest::refer();
		if (!$_SESSION['loginrefer'] || $force) $_SESSION['loginrefer'] = $uri;
	}

	static function logout() {
		unset($_SESSION['user']);
	}

}