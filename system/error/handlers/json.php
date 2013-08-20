<?php namespace System\Error\Handlers;

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

use System\Error\Handler;

class Json extends Handler {

	public function response() {
		if( ! headers_sent()) {
			header('Status: 500 Internal Server Error', true);
			header('Content-Type: application/json', true);
		}

		echo json_encode(array('exception' => array(
			'message' => $this->exception->getMessage(),
			'file' => $this->exception->getFile(),
			'line' => $this->exception->getLine()
		)));
	}

}