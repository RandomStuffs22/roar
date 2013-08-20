<?php namespace System\Database\Connectors;

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

use PDO;
use System\Database\Connector;

class Mysql extends Connector {

	/**
	 * The mysql wrapper
	 *
	 * @var string
	 */
	public $wrapper = '`%s`';

	/**
	 * Create a new mysql connector
	 *
	 * @param array
	 */
	protected function connect($config) {
		extract($config);

		$dns = 'mysql:' . implode(';', array('dbname=' . $database, 'host=' . $hostname, 'port=' . $port, 'charset=' . $charset));
		$options = array();

		if(version_compare(PHP_VERSION, '5.3.6', '<')) {
			$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $charset;
		}

		return new PDO($dns, $username, $password, $options);
	}

}