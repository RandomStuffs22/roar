<?php

/**
 * Roar
 */

define('START_TIME', microtime(true));
define('DS', DIRECTORY_SEPARATOR);
define('ENV', getenv('APP_ENV'));
define('VERSION', '0.1');

define('PATH', dirname(__FILE__) . DS);
define('APP', PATH . 'roar' . DS);
define('SYS', PATH . 'system' . DS);
define('EXT', '.php');

require SYS . 'start' . EXT;