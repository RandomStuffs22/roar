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
use System\Database\Query;

class Database extends Driver {

	protected $exists = false;

	public function read($id) {
		extract($this->config);

		// run garbage collection
		if(mt_rand(0, 100) > 90) {
			Query::table($table)->where('expire', '<', time())->delete();
		}

		// find session
		$query = Query::table($table)->where('id', '=', $id)->where('expire', '>', time());

		if($result = $query->fetch(array('data'))) {
			$this->exists = true;

			if($data = unserialize($result->data)) {
				return $data;
			}
		}
	}

	public function write($id, $cargo) {
		extract($this->config);

		// if the session is set to never expire
		// we will set it to 1 year
		if($lifetime == 0) {
			$lifetime = (3600 * 24 * 365);
		}

		$expire = time() + $lifetime;
		$data = serialize($cargo);
		$query = Query::table($table);

		if($this->exists) {
			$query->where('id', '=', $id)->update(array('expire' => $expire, 'data' => $data));
		}
		else {
			$query->insert(array('id' => $id, 'expire' => $expire, 'data' => $data));
		}
	}

}