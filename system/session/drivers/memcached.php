<?php namespace System\Session\Drivers;

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

use Memcached as M;
use System\Config;
use System\Session\Driver;

class Memcached extends Driver {

	protected $server;

	public function __construct($config, $key) {
		$this->config = $config;
		$this->key = $key;
		$this->server = new M;

		foreach(Config::cache('memcache', array()) as $server) {
			$this->server->addServer($server['host'], $server['port']);
		}
	}

	public function read($id) {
		if($data = $this->server->get($this->key . '_' . $id)) {
			return unserialize($data);
		}
	}

	public function write($id, $cargo) {
		extract($this->config);

		// if the session is set to never expire
		// we will set it to 1 year
		if($lifetime == 0) {
			$lifetime = (3600 * 24 * 365);
		}

		$this->server->set($this->key . '_' . $id, serialize($cargo), $lifetime);
	}

}