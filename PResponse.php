<?php
class PResponse
{
    static function redirect($s=NULL, $code=NULL)
    {
        if (!$s) $s = PRequest::refer();
        if ($code==301) header('HTTP/1.1 301 Moved Permanently');
        header('location: '.$s);
        return '<p>This page is moved to <a href="'.$s.'">'.$s.'</a></p>';
    }

    static function json($data=array())
    {
        header('Content-Type: application/x-javascript');
        return json_encode($data);
    }

    /**
     * 消息展示
     * @param int $status 状态 {0:失败, 1:提示, 2:成功}
     * @param string $msg
     * @param array $urls
     * @param int $time
     * @param array $data
     * @param bool $json
     */
    static function cmsg($status, $msg, $urls = null, $time = 0, $data = array(), $json = null)
    {
        if (is_null($urls)) $urls = array(array('link'=>PRequest::refer(), 'title'=>'返回上一页'));
        if (!is_array($urls)) $urls = array();
        if ($urls['link']) $urls = array($urls);
        $urls[] = array('link'=>'/', 'title'=>'返回首页');
        $data['MSG_STATUS'] = $status;
        $data['MSG_MESSAGE'] = $msg;
        $data['MSG_URLS'] = $urls;
        $data['MSG_TIME'] = $time;
        if (is_null($json)) $json = PRequest::ajax();
        if ($json) {
            return self::json($data);
        } else {
            return $this->renderFile(PMVC::$approot.'/view/cmsg.php', $data);
        }
    }
}