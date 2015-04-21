<?php
class PAuthController extends PController
{
	function before()
	{
		parent::before();
		session_start();
		if (is_array($_SESSION['user']) && $_SESSION['user']['id'] && !$_SESSION['user']['username']) {
			$_SESSION['user'] = S('user')->get($_SESSION['user']['id']);
		}
	}
}