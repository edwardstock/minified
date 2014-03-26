<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: HTTP
 */

namespace common\helpers;


class HTTP {

	public static function buildUrl(array $params) {
		$url = "";

		$i = 0;
		foreach($params AS $paramName=>$value) {
			if($i === 0){
				$url .= $paramName.'='.urlencode($value);
			} else {
				$url .= '&'.$paramName.'='.urlencode($value);
			}

			$i++;
		}

		return $url;
	}

	public static function fixGettingQueryString($mainParam) {
		$out = array();
		$out[$mainParam] = '';
		foreach($_GET AS $name=>$value) {

			if($name === $mainParam){
				$out[$mainParam] = $value;
			} else {
				$out[$mainParam] .= '&'.$name.'='.$value;
			}
		}

		$_GET = $out;
	}
} 