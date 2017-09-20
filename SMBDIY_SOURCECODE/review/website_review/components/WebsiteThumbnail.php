<?php
class WebsiteThumbnail {
	public static function getUrl(array $params=array(), $server=0) {
		if(!isset(Yii::app()->params["thumbs"][$server])) {
			throw new Exception("Invalid thumbnail server"); 
		}
		$config=Yii::app()->params["thumbs"][$server];
		$link=array_shift($config);
		$config=array_merge($config, $params);
		return self::prepareUrl($link, $config);
	}
	
	public static function prepareUrl($url, array $params =array()) {
		$default=array(
			'{{Protocol}}'=>Yii::app()->request->IsSecureConnection ? 'https' : 'http',
		);
		$params=array_merge($default, $params);
		$params=array_map("urlencode", $params);
		return strtr($url, $params);
	}
	
	public static function thumbnailStack($websites, array $params=array()) {
		$count=count(Yii::app()->params["thumbs"]);
		$counter=0;
		$stack=array();
		foreach($websites as $website) {
			$index=$counter%$count;
			$params['{{Url}}']=$website['domain'];
			$stack[$website['id']]=self::getUrl($params, $index);
			$counter++;
		}
		return $stack;
	}
}