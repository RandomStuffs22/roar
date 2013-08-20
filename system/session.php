<?php namespace System;

/**
 * Nano
 *
 * Just another php framework
 *
 * @package		nano
 * @link		http://madebykieron.co.uk
 * @copyright	Copyright 2013 Kieron Wilson
 * @license		http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use ErrorException;
use ReflectionClass;
use System\Session\Cargo;

class Session {

	/**
	 * Holds an instance of the session driver
	 *
	 * @var array
	 */
	protected static $cargo;

	/**
	 * Create a new session driver object
	 *
	 * @param array
	 */
	public static function factory($config) {
		$ref = new ReflectionClass('\\' . __NAMESPACE__ . '\\Session\\Drivers\\' . ucfirst($config['driver']));

		return $ref->newInstance($config, Config::app('key'));
	}

	/**
	 * Returns the curren instance of the cargo object
	 *
	 * @return object Cargo
	 */
	public static function instance() {
		if(is_null(static::$cargo)) {
			$driver = static::factory(Config::session());

			static::$cargo = new Cargo($driver);
		}

		return static::$cargo;
	}

	/**
	 * Magic method to call a method on the session driver
	 *
	 * @param string
	 * @param array
	 */
	public static function __callStatic($method, $arguments) {
		return call_user_func_array(array(static::instance(), $method), $arguments);
	}

}