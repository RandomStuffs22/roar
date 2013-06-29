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
	$input = Input::get(array('name', 'email', 'username', 'password'));

	$validator = new Validator($input);

	$validator->check('name')
		->is_max(3, 'Please enter your name');

	$validator->check('email')
		->is_email('Please enter your email address');

	$validator->add('unquie_username', function($str) {
		return User::where('username', 'like', trim($str))->count() == 0;
	});

	$validator->check('username')
		->is_alnum('Usernames can only contain letter and numbers')
		->is_unquie_username('Username is already taken')
		->is_max(5, 'Please enter a username');

	$validator->check('password')
		->is_max(6, 'Please enter a secure password');

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('register');
	}

	$user = User::create(array(
		'role' => 'user',
		'registered' => Date::mysql(),
		'name' => $input['name'],
		'email' => $input['email'],
		'username' => $input['username'],
		'password' => Hash::password($input['password'])
	));

	Session::put(Auth::$session, $user->id);

	Notify::success('Your account has been created');

	return Response::redirect('/');
});

/*
	404 catch all
*/
Route::not_found(function() {
	$view = new Template('404');

	return Response::create($view->yield(), 404);
});