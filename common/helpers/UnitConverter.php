<?php
/**
 * Created by PhpStorm.
 * User: edwardstock
 * Date: 21.03.14
 * Time: 12:36
 */
namespace common\helpers;

class UnitConverter {

	public static function convertBytes($number) {
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		return @round($number / pow(1024, ($i = floor(log($number, 1024)))), 2) . ' ' . $unit[$i];
	}
} 