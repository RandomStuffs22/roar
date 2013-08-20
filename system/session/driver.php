<?php namespace System\Session;

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

abstract class Driver {

	/**
	 * The session config array
	 *
	 * @var array
	 */
	public $config;

	/**
	 * The application key
	 *
	 * @var array
	 */
	public $key;

	/**
	 * Create a new instance of a driver
	 *
	 * @param array
	 */
	public function __construct($config, $key) {
		$this->config = $config;
		$this->key = $key;
	}

	/**
	 * The session read prototype
	 */
	abstract public function read($id);

	/**
	 * The session write prototype
	 *
	 * @param int
	 * @param object
	 */
	abstract public function write($id, $cargo);

}