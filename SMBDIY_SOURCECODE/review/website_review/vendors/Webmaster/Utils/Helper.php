<?php
class Helper {
	public static function striptags($html) {
		$html = preg_replace('/(<|>)\1{2}/is', '', $html);
		$search = array(
			'#<style[^>]*?>.*?</style>#si', // Strip style tags properly
            '#<script[^>]*?>.*?</script>#si',// Strip out javascript
			'#<!--.*?>.*?<*?-->#si', // Strip if
			'#<[\/\!]*?[^<>]*?>#si',         // Strip out HTML tags*/
			'#<![\s\S]*?--[ \t\n\r]*>#si',  // Strip multi-line comments including CDATA
		);
		$html = preg_replace($search, " ", $html);
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $html);
		$html = preg_replace('#(<\/[^>]+?>)(<[^>\/][^>]*?>)#i', '$1 $2', $html);
		return $html;
	}

	public static function isEmptyArray($array) {
		foreach($array as $value) {
			if(is_array($value)) {
				if(self::isEmptyArray($value) == false)
					return false;
			} else {
				return empty($value);
			}
		}
		return true;
	}
}