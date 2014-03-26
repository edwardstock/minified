<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: Html
 */

namespace common\helpers;
use yii;

class Html extends yii\helpers\Html{

	public static function encodeArray(array $source, $doubleEncode = true) {
		$out = array();

		foreach($source AS $key=>$value) {
			$out[self::encode($key, $doubleEncode)] = self::encode($value, $doubleEncode);
		}

		return $out;
	}
}