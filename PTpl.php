<?php
class PTpl {
	
	static function js($s) {
		if (!is_array($s)) $s = explode(',', $s);
		PMVC::$js = array_merge(PMVC::$js, $s);
	}
	
	static function css($s) {
		if (!is_array($s)) $s = explode(',', $s);
		PMVC::$css = array_merge(PMVC::$css, $s);
	}
	
}