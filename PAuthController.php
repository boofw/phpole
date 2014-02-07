<?php
class PAuthController extends PController {

	protected $visitor = array();

	function before() {
		parent::before();
		session_start();
		if (is_array($_SESSION['user'])) $this->visitor = $_SESSION['user'];
		if ($this->visitor['id'] && !$this->visitor['username']) {
			$this->visitor = S('user')->get($this->visitor['id']);
		}
	}

	function loginReqired($returnUrl=NULL) {
		if (!$_SESSION['user']['id']) {
			if (!$returnUrl) {
				$returnUrl = $_SERVER['REQUEST_URI'];
			}
			$_SESSION['loginrefer'] = $returnUrl;
			$this->redirect('/auth/login');
		} elseif (is_string($returnUrl) && $returnUrl) {
			$this->redirect($returnUrl);
		}
	}

	function login($user) {
		if (!is_array($user)) $user = array('id'=>intval($user));
		$_SESSION['user'] = $user;
		$this->loginRefer();
	}

	function loginRefer() {
		($refer=$_SESSION['loginrefer']) || ($refer=$this->refer);
		unset($_SESSION['loginrefer']);
		$this->redirect($refer);
	}
	
	function setLoginReferUri($uri=NULL, $force=TRUE) {
		if (!$uri) $uri = $this->refer;
		if (!$_SESSION['loginrefer'] || $force) $_SESSION['loginrefer'] = $uri;
	}

	function logout() {
		unset($_SESSION['user']);
	}

}