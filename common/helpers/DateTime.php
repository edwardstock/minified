<?php
/**
 * Created by PhpStorm.
 * User: edwardstock
 * Date: 21.03.14
 * Time: 13:03
 */

namespace common\helpers;


class DateTime {

	const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
	const MYSQL_DATE_FORMAT = 'Y-m-d';

	/**
	 * Returns current datetime
	 * @return bool|string
	 */
	public static function getDateTime() {
		return date(self::MYSQL_DATETIME_FORMAT);
	}

	/**
	 * Returns current date
	 * @return bool|string
	 */
	public static function getDate() {
		return date(self::MYSQL_DATE_FORMAT);
	}

	/**
	 * Converts datetime format to timestamp (using @see{strtotime()})
	 * @param string $dateTime
	 * @return int
	 */
	public static function dateTimeToTimestamp($dateTime) {
		return strtotime($dateTime);
	}

	/**
	 * Converts timestamp do datetime format
	 * @param integer $timestamp
	 * @return bool|string
	 */
	public static function timestampToDateTime($timestamp) {
		return date(self::MYSQL_DATETIME_FORMAT, $timestamp);
	}

	/**
	 * @param integer $timestamp
	 * @return bool|string
	 */
	public static function timestampToDate($timestamp) {
		return date(self::MYSQL_DATE_FORMAT, $timestamp);
	}

	/**
	 * @param string|int $datetime Must be datetime in string format Y-m-d H:i:s OR !!IS INTEGER number timestamp
	 * @param bool $asStamp
	 * @param int $seconds
	 * @param int $minutes
	 * @param int $hours
	 * @param int $days
	 * @param int $weeks
	 * @param int $months
	 * @param int $years
	 * @return int|string
	 */
	public static function getAddedTime($datetime, $asStamp=false, $seconds=0, $minutes=0, $hours=0, $days=0, $weeks=0, $months=0, $years=0) {
		$timestamp = is_integer($datetime) ? $datetime : self::dateTimeToTimestamp($datetime);

		$daysInCurrentMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$month = $day * $daysInCurrentMonth;
		$year = $month * 12;

		$newStamp = (int) $timestamp +
			($seconds +
				($minutes+$minute) +
				($hours+$hour) +
				($days+$day) +
				($weeks+($day*7)) +
				($months+$month)+($years+$year)
			);

		if($asStamp)
			return $newStamp;

		return date('Y-m-d H:i:s', $newStamp);
	}


} 