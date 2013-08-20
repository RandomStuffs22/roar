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

use System\Session\Driver;

class Apc extends Driver {

	public function read($id) {
		if($data = apc_fetch($this->key . '_' . $id)) {
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

		apc_store($this->key . '_' . $id, serialize($cargo), $lifetime);
	}

}