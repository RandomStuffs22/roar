<?php

class Date {

	/*
	 * Format a date as per users timezone and format
	 */
	public static function format($date = 'now', $format = 'jS F, Y') {
		$date = new DateTime($date, new DateTimeZone('GMT'));
		$date->setTimezone(new DateTimeZone(Config::app('timezone')));

		return $date->format($format);
	}

	/*
	 * All database dates are stored as GMT
	 */
	public static function mysql($date = 'now') {
		$date = new DateTime($date, new DateTimeZone('GMT'));

		return $date->format('Y-m-d H:i:s');
	}

	/*
	 * Human readable relative time
	 */
	public static function relative($date, $format = 'jS F, Y') {
		// format database date into current timezone
		$date = new DateTime($date, new DateTimeZone('GMT'));
		$date->setTimezone(new DateTimeZone(Config::app('timezone')));

		// current time in current timezone
		$now = new DateTime('now');

		// diff in seconds
		$diff = $now->getTimestamp() - $date->getTimestamp();

		if($diff > 172800) { // 2 days
			return $date->format($format);
		}

		if($diff > 86400) { // 1 day
			return $date->format('H:i') . ' Yesterday';
		}

		if($diff > 43200) { // 12 hours
			return $date->format('H:i') . ' Today';
		}

		if($diff > 21600) { // 6 hours
			return $date->format('H:i');
		}

		if($diff > 3600) { // 1 hour
			return ceil($diff / 3600) . ' hours ago';
		}

		if($diff > 60) { // 1 min
			return ceil($diff / 60) . ' minutes ago';
		}

		return 'Just now';
	}

}