<?php

/*
	List all
*/
Route::get(array('admin/users', 'admin/users/(:num)'), array('before' => 'auth', 'main' => function($page = 1) {
	$vars['messages'] = Notify::read();

	$users = User::take(10)->skip(10 * ($page - 1))->get();
	$count = User::count();

	$vars['users'] = new Paginator($users, $count, $page, 10, 'admin/users');

	return View::create('users/index', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

/*
	Edit
*/
Route::get('admin/users/edit/(:num)', array('before' => 'auth', 'main' => function($id) {
	$vars['messages'] = Notify::read();
	$vars['token'] = Csrf::token();
	$vars['user'] = User::find($id);
	$vars['roles'] = array('administrator' => __('users.administrator'), 'user' => __('users.user'));

	return View::create('users/edit', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

Route::post('admin/users/edit/(:num)', array('before' => 'auth', 'main' => function($id) {
	$input = Input::get_array(array('role', 'name', 'email', 'username', 'password'));
	$password_reset = false;

	if($password = Input::get('password')) {
		$input['password'] = $password;
		$password_reset = true;
	}

	$validator = new Validator($input);

	$validator->check('username')
		->is_max(3, __('users.missing_username'));

	$validator->check('email')
		->is_email(__('users.missing_email'));

	if($password_reset) {
		$validator->check('password')
			->is_max(6, sprintf(__('users.password_too_short'), 6));
	}

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('admin/users/edit/' . $id);
	}

	if($password_reset) {
		$input['password'] = Hash::make($input['password']);
	}

	User::update($id, $input);

	Notify::success(__('users.user_success_updated'));

	return Response::redirect('admin/users/edit/' . $id);
}));

/*
	Add
*/
Route::get('admin/users/add', array('before' => 'auth', 'main' => function() {
	$vars['messages'] = Notify::read();
	$vars['token'] = Csrf::token();
	$vars['statuses'] = array('inactive' => __('users.inactive'), 'active' => __('users.active'));
	$vars['roles'] = array('administrator' => __('users.administrator'), 'editor' => __('users.editor'), 'user' => __('users.user'));

	return View::create('users/add', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

Route::post('admin/users/add', array('before' => 'auth', 'main' => function() {
	$input = Input::get_array(array('role', 'name', 'email', 'username', 'password'));

	$validator = new Validator($input);

	$validator->check('username')
		->is_max(3, __('users.missing_username'));

	$validator->check('email')
		->is_email(__('users.missing_email'));

	$validator->check('password')
		->is_max(6, sprintf(__('users.password_too_short'), 6));

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('admin/users/add');
	}

	$input['password'] = Hash::make($input['password']);

	User::create($input);

	Notify::success(__('users.user_success_created'));

	return Response::redirect('admin/users');
}));
