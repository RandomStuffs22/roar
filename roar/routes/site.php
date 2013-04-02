<?php

Route::action('auth', function() {
	if(Auth::guest()) {
		return Response::redirect('admin/login');
	}
	else {
		$user = Auth::user();

		if($user->role != 'administrator') return Response::redirect('admin/login');
	}
});

Route::action('auth-user', function() {
	if(Auth::guest()) {
		Notify::error('Please login to continue');

		return Response::redirect('/');
	}
});

Route::action('csrf', function() {
	if( ! Csrf::check(Input::get('token'))) {
		Notify::error(array('Invalid token'));

		return Response::redirect('admin/login');
	}
});

/*
	Login
*/
Route::get('login', function() {
	return new Template('login');
});

Route::post('login', function() {
	if( ! Auth::attempt(Input::get('username'), Input::get('password'))) {
		Input::flash();

		Notify::error('Invalid details');

		return Response::redirect('login');
	}

	return Response::redirect('discussions');
});

/*
	Logout
*/
Route::get('logout', function() {
	Auth::logout();

	return Response::redirect('/');
});

/*
	Register
*/
Route::get('register', function() {
	return new Template('register');
});

Route::post('register', function() {
	$input = array(
		'name' => Input::get('name'),
		'email' => Input::get('email'),
		'username' => Input::get('username'),
		'password' => Input::get('password')
	);

	$validator = new Validator($input);

	$validator->check('name')
		->is_max(3, 'Please enter your name');

	$validator->check('email')
		->is_email('Please enter your email address');

	$validator->add('unquie_username', function($str) {
		$user = User::search(array('username' => $str));

		return ! isset($user->id);
	});

	$validator->check('username')
		->is_unquie_username('Username is already taken')
		->is_max(5, 'Please enter a username');

	$validator->check('password')
		->is_max(6, 'Please enter a secure password');

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('register');
	}

	User::create(array(
		'role' => 'user',
		'registered' => gmdate('Y-m-d H:i:s'),
		'name' => $input['name'],
		'email' => $input['email'],
		'username' => $input['username'],
		'password' => Hash::password($input['password'])
	));

	$user = User::search(array('username' => $input['username']));

	Session::put(Auth::$session, $user);

	Notify::success('Your account has been created');

	return Response::redirect('/');
});

/*
	404 catch all
*/
Route::error('404', function() {
	return Response::error(404);
});