<?php
/**
 * Service - DAO 桥
 * 
 * 通过 Service 调用 DAO 中的方法，使 DAO 的功能可在远程应用中直接调用
 * 
 * @author pole
 */
class PDbService extends PService
{
	protected $dbtbl = '';
	
	public static function init($name, $appid) {
		if (substr($name, 0, 3)=='db:') {
			$d = explode(':', $name);
			$obj = parent::init('PDb', $appid);
			$obj->dbtbl = substr($name, 3);
			return $obj;
		} else {
			return parent::init($name, $appid);
		}
	}
	
	public function __call($method, $args) {
		return call_user_func_array(array(PDAO::init($this->dbtbl), $method), $args);
	}
}