<?php
class PUtil {

	static function mkdir($dir, $mode=0755) {
		if (!is_dir($dir)) {
			mkdir($dir, $mode, true);
		}
	}

	static function hdate($time, $now=NULL) {
		if (!$now) $now = $_SERVER['REQUEST_TIME'];
		if (!$time) $time = $now;
		$d = date_diff(date_create('@'.$time), date_create('@'.$now));
		if ($d->y) return $d->y.'年前';
		elseif ($d->m) return $d->m.'个月前';
		elseif ($d->d) return $d->d.'天前';
		elseif ($d->h) return $d->h.'小时前';
		elseif ($d->i) return $d->i.'分钟前';
		else return '刚刚';
	}

	static function meta($s, $len=120) {
		$s = preg_replace('/\[simg\](.{17})\[\/simg\]/', '', $s);
		$s = html_entity_decode(strip_tags($s));
		$s = str_replace(array('　',"\n","\r","\t"), ' ', $s);
		$s = preg_replace('/\s{2,}/', ' ', $s);
		$s = trim($s);
		if ($len) $s = mb_substr($s, 0, $len, 'utf-8');
		return $s;
	}
	
	static function metaPre($s, $len=0) {
		$s = nl2br($s);
		$s = preg_replace('/\s{2,}/', ' ', $s);
		$s = str_replace('<p', "\n<p", $s);
		$s = str_replace('<div', "\n<div", $s);
		$s = str_replace('<br', "\n<br", $s);
		$s = str_replace('<li', "\n<li", $s);
		$s = strip_tags($s);
		$s = str_replace("\r", "\n", $s);
		$s = preg_replace('/\n{2,}/', "\n", $s);
		$s = trim($s);
		if ($len) $s = mb_substr($s, 0, $len, 'utf-8');
		$s = nl2br($s);
		return $s;
	}

	static function dec2xx($num, $base=62) {
		if ($base>62) $base = 62;
		$index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$t = ($num == 0) ? 0 : floor(log10($num) / log10($base));
		for ( $t; $t >= 0; $t-- ) {
			$a = floor( $num / pow( $base, $t ) );
			$out = $out . substr( $index, $a, 1 );
			$num = $num - ( $a * pow( $base, $t ) );
		}
		return $out;
	}

	static function hex36($hex) {
		$n = hexdec($hex);
		return self::dec2xx($n, 36);
	}
	
	static function pager($data, $baseurl = '', $pageicon = 10, $type = 2, $pagekey = 'page') {
		$s = '';
		$ux = parse_url($baseurl);
		parse_str($ux['query'], $uargs);
		unset($uargs[$pagekey]);
		$ux['query'] = http_build_query($uargs);
		if ($ux['query']) $ux['query'] .= '&';
		if ($ux['scheme']) $ux['scheme'] .= '://';
		$baseurl = $ux['scheme'].$ux['host'].$ux['path'].'?'.$ux['query'].$pagekey.'=';
		if ($data['page']<1) $data['page'] = 1;
		$half = floor(($pageicon-1)/2);
		$min = $data['page']-$half;
		if ($min<1) $min=1;
		$max=$min+$pageicon;
		if ($max>$data['pagemax']+1) $max=$data['pagemax']+1;
		$min = $max-$pageicon;
		if ($min<1) $min=1;
	
		if (!$type) {
			if ($data['page']<2) $dispre .= ' class="disabled"';
			$curp = $data['page']-1;
			$curp = $curp>0 ? $curp : 1;
			$s .= '<li'.$dispre.'><a href="'.$baseurl.$curp.'" data-page="'.$curp.'">«</a></li>';
		}
		if ($data['page']>1) {
			if (in_array($type, array(2))) $s .= '<li><a href="'.$baseurl.'1" data-page="1">首页</a></li>';
			if (in_array($type, array(1,2))) {
				$curp = $data['page']-1;
				$curp = $curp>0 ? $curp : 1;
				$s .= '<li><a href="'.$baseurl.$curp.'" data-page="'.$curp.'">上一页</a></li>';
			}
		}
		if ($data['pagemax']>1 || !in_array($type, array(1,2))) {
			for ($i=$min; $i<$max; $i++) {
				if ($i==$data['page']) $s .= '<li class="active"><a href="'.$baseurl.$i.'" data-page="'.$i.'">'.$i.'</a></li>';
				else $s .= '<li><a href="'.$baseurl.$i.'" data-page="'.$i.'">'.$i.'</a></li>';
			}
		}
		if ($data['page']<$data['pagemax']) {
			if (in_array($type, array(1,2))) {
				$s .= '<li><a href="'.$baseurl.($data['page']+1).'" data-page="'.($data['page']+1).'">下一页</a></li>';
			}
			if (in_array($type, array(2))) $s .= '<li><a href="'.$baseurl.$data['pagemax'].'" data-page="'.$data['pagemax'].'">尾页</a></li>';
		}
		if (!$type) {
			if ($data['page']>$data['pagemax']-1) {
				$disend .= ' class="disabled"';
				$data['page'] = $data['pagemax']-1;
			}
			$s .= '<li'.$disend.'><a href="'.$baseurl.($data['page']+1).'" data-page="'.($data['page']+1).'">»</a></li>';
		}
	
		if ($s) $s = '<ul>'.$s.'</ul>';
		return $s;
	}
	
	static function num2str($num, $max=999) {
		$r = $num.'';
		if ($max<10 && $num>9) $r = '9+';
		elseif ($max<100 && $num>99) $r = '99+';
		elseif ($max>998) {
			if ($num>9999) $r = floor($num/10000).'万';
			elseif ($num>999) $r = floor($num/1000).'千';
		}
		return $r;
	}
	
	static function qrcode($str, $size='200x200') {
		return 'http://chart.apis.google.com/chart?chs='.$size.'&cht=qr&chld=L|0&chl='.urlencode($str);
	}
	
	static function getEmailUrl($email)
	{
		$mailurls = array(
				'@163.com' => 'http://mail.163.com',
				'@126.com' => 'http://mail.126.com',
				'@yeah.net' => 'http://mail.yeah.net',
				'@sina.com' => 'http://mail.sina.com.cn',
				'@qq.com' => 'http://mail.qq.com',
				'@sohu.com' => 'http://mail.sohu.com',
				'@tom.com' => 'http://mail.tom.com',
				'@yahoo.com' => 'http://mail.yahoo.com',
				'@yahoo.com.cn' => 'http://mail.cn.yahoo.com',
				'@yahoo.cn' => 'http://mail.cn.yahoo.com',
				'@21cn.com' => 'http://mail.21cn.com',
				'@gmail.com' => 'http://mail.google.com',
				'@hotmail.com' => 'http://outlook.com',
				'@live.com' => 'http://outlook.com',
		);
		$mx = strrchr($email, '@');
		if ($mailurls[$mx]) return $mailurls[$mx];
		else return 'javascript:;';
	}
	
	static function hideEmail($email) {
		$shadow = '';
		$len = strpos($email, '@');
		for ($i=1; $i<$len; $i++) {
			$shadow .= '*';
		}
		return substr($email, 0, 1).$shadow.strrchr($email, '@');
	}
	
	static function stripCss($s) {
		$s = preg_replace('/\s+style\=\"[^\"]*\"/is', '', $s);
		$s = preg_replace('/\s+style\=\'[^\']*\'/is', '', $s);
		$s = preg_replace('/\s+class\=\"[^\"]*\"/is', '', $s);
		$s = preg_replace('/\s+class\=\'[^\']*\'/is', '', $s);
		$s = preg_replace('/\s+on[^\=]+\=\"[^\"]*\"/is', '', $s);
		$s = preg_replace('/\s+on[^\=]+\=\'[^\']*\'/is', '', $s);
		$s = preg_replace('/<script.*?<\/script>/is', '', $s);
		$s = preg_replace('/<iframe.*?<\/iframe>/is', '', $s);
		$s = preg_replace('/<style.*?<\/style>/is', '', $s);
		$s = preg_replace('/<link.*?>/is', '', $s);
		return $s;
	}
	
	static function mkListData($array, $k='id', $v='name', $pre=null) {
		if (!is_array($array)) $array = array();
		$r = array();
		if (is_array($pre)) $r = $pre;
		foreach ($array as $d) {
			$r[$d[$k]] = $d[$v];
		}
		return $r;
	}
	
	static function urlArgs($a, $uri=null) {
			
		if (!$uri) $uri = $_SERVER['REQUEST_URI'];
		$ux = parse_url($uri);
		parse_str($ux['query'], $uargs);
		$uargs = array_merge($uargs, $a);
		if ($uargs['page']) unset($uargs['page']);
		$ux['query'] = http_build_query($uargs);
		$r = $ux['path'].'?'.$ux['query'];
		if ($ux['fragment']) $r .= '#'.$ux['fragment'];
		return $r;
	}
	
	static function urlDelArg($a, $uri=null) {
		if (!$uri) $uri = $_SERVER['REQUEST_URI'];
		$ux = parse_url($uri);
		parse_str($ux['query'], $uargs);
		if (!is_array($a)) $a = array($a);
		foreach ($a as $v) {
			unset($uargs[$v]);
		}
		$ux['query'] = http_build_query($uargs);
		$r = $ux['path'].'?'.$ux['query'];
		if ($ux['fragment']) $r .= '#'.$ux['fragment'];
		return $r;
	}
}