<?php

/*
 * Set your applications current timezone
 */
date_default_timezone_set(Config::app('timezone', 'UTC'));

/*
 * Define the application error reporting level based on your environment
 * using the ENV constant.
 *
 * You can set the APP_ENV var in your htaccess or webserver to switch
 * between environments or change the code below to detect a url or
 * anthing thing you want ...
 */
switch(constant('ENV')) {
	case 'dev':
		ini_set('display_errors', true);
		error_reporting(-1);
		break;

	default:
		ini_set('display_errors', true);
		error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
}

/*
 * Set autoload directories to include your app models and libraries
 */
Autoloader::directory(array(
	APP . 'models',
	APP . 'libraries'
));

/*
	Application preprocessing
*/
/*
if( ! is_readable(APP . 'config/database.php')) {
	// go to installer
	header('Location: ' . rtrim($_SERVER['REQUEST_URI'], '/') . '/install/');
	exit;
}
*/

// load settings
foreach(Query::table('settings')->get() as $item) {
	$settings[$item->key] = $item->value;
}

Config::set('settings', $settings);

// theme functions
$fi = new FilesystemIterator(APP . 'functions', FilesystemIterator::SKIP_DOTS);

foreach($fi as $file) {
	if($file->isFile() and $file->isReadable() and
		pathinfo($file->getBasename(), PATHINFO_EXTENSION) == 'php') {
		require $file->getPathname();
	}
}

// language helper
function __($line, $default = 'No language replacement') {
	$args = array_slice(func_get_args(), 2);

	return Language::line($line, $default, $args);
}

// admin helpers
function admin_asset($path) {
	return rtrim(Config::get('application.url'), '/') . '/roar/views/assets/' . ltrim($path, '/');
}

function admin_url($path) {
	return base_url('admin/' . $path);
}

function get_twitter_api() {
	return new Twitter(Config::get('settings.twitter_consumer_key'), Config::get('settings.twitter_consumer_secret'));
}

function slug($str, $separator = '-') {
	$str = normalize($str);

	// replace non letter or digits by separator
	$str = preg_replace('#[^\\pL\d]+#u', $separator, $str);

	return trim(strtolower($str), $separator);
}

/**
 * Import defined routes
 */
foreach(glob(APP . 'routes/*' . EXT) as $file) require $file;
foreach(glob(APP . 'routes/admin/*' . EXT) as $file) require $file;