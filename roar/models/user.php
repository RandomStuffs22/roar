<?php

class User extends Record {

	public static $table = 'users';

	public static function search($params) {
		foreach($params as $key => $value) {
			if( ! isset($query)) $query = static::where($key, '=', $value);
			else $query->where($key, '=', $value);
		}

		return $query->fetch();
	}

}