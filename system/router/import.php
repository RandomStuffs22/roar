<?php namespace System\Router;

/**
 * Nano
 *
 * Just another php framework
 *
 * @package		nano
 * @link		http://madebykieron.co.uk
 * @copyright	http://unlicense.org/
 */

class Import {

	/**
	 * Create a new instance of the Router class and import
	 * app routes from a folder or a single routes.php file
	 *
	 * @param string
	 * @param string
	 */
	public function __construct($uri) {
		// read all files and nested files
		if(is_dir($path = APP . 'routes')) {
			$this->read($path, $uri);
		}

		// read single file
		if(is_readable($path = APP . 'routes' . EXT)) {
			require $path;
		}
	}

	/**
	 * Try and match uri with filesystem so we only include
	 * files relative to our uri and not everything
	 *
	 * @param string
	 */
	private function read($path, $uri) {
		// direct match
		if(file_exists($file = $path . DS . $uri . EXT)) {
			require $file;
		}

		// try matching a folder
		$segments = array_diff(explode('/', $uri), array(''));

		if(count($segments)) {
			while(count($segments) and is_dir($path . DS . $segments[0])) {
				// if we have a same name file import it
				if(file_exists($file = $path . DS . $segments[0] . EXT)) require $file;

				// step into dir shift one from the array
				$path .= DS . array_shift($segments);
			}

			// if the whole uri matched a folder import the folder
			if(empty($segments)) return $this->import($path);

			// try matching a file with remaining segemnts
			while(count($segments)) {
				$segment = array_shift($segments);

				if(file_exists($file = $path . DS . $segment . EXT)) {
					return require $file;
				}
			}
		}
	}

	/**
	 * Import app routes from a directory
	 *
	 * @param string
	 */
	private function import($path) {
		$iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);

		foreach($iterator as $fileinfo) {
			if('.' . $fileinfo->getExtension() == EXT) {
				require $fileinfo->getPathname();
			}
		}
	}

}