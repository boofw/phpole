<?php
/**
 * Init a BaseService or ApiClient
 *
 * 相当于 PDbService::init($name, $appid)
 * 相当于 PApiClient::init(API_URL, API_APPID, API_APPKEY, $name);
 *
 * @param srting $name
 * @param srting $appid C/S 模式时服务端自动处理，应用中禁止直接使用
 *
 * @return PDbService
 *
 * @example S(folder.className);
 */
function S($name, $appid = null) {
	if (defined('API_URL') && defined('API_APPID') && defined('API_APPKEY')) {
		return PApiClient::init(API_URL, API_APPID, API_APPKEY, $name);
	}
	if (is_null($appid) && defined('API_APPID')) $appid = API_APPID;
	return PDbService::init($name, $appid);
}

/**
 * Init a PDAO
 *
 * 相当于 PDAO::init($name)
 *
 * @param string $name
 *
 * @return PDAO
 *
 * @example D(dbName.tableName);
 */
function D($name) {
	if (defined('API_URL') && defined('API_APPID') && defined('API_APPKEY')) {
		return PApiClient::init(API_URL, API_APPID, API_APPKEY, 'db:'.$name);
	}
	return PDAO::init($name);
}