<?php

/*
	Main welcome page
*/
Route::get('admin/dashboard', array('before' => 'auth', 'main' => function() {
	$vars['messages'] = Notify::read();
	$vars['token'] = Csrf::token();

	return View::create('dashboard', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));