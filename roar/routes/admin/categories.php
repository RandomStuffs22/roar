<?php

/*
	List all
*/
Route::get(array('admin/categories', 'admin/categories/(:num)'), array('before' => 'auth', 'main' => function($page = 1) {
	$vars['messages'] = Notify::read();

	$categories = Category::take(10)->skip(10 * ($page - 1))->get();
	$count = Category::count();

	$vars['categories'] = new Paginator($categories, $count, $page, 10, 'admin/categories');

	return View::create('categories/index', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

/*
	Edit
*/
Route::get('admin/categories/edit/(:num)', array('before' => 'auth', 'main' => function($id) {
	$vars['messages'] = Notify::read();
	$vars['token'] = Csrf::token();
	$vars['category'] = Category::find($id);

	return View::create('categories/edit', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

Route::post('admin/categories/edit/(:num)', array('before' => 'auth', 'main' => function($id) {
	$input = Input::get_array(array('title', 'slug', 'description'));

	$validator = new Validator($input);

	$validator->check('title')
		->is_max(3, __('categories.missing_title'));

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('admin/categories/edit/' . $id);
	}

	if(empty($input['slug'])) {
		$input['slug'] = $input['title'];
	}

	$input['slug'] = Str::slug($input['slug']);

	Category::update($id, $input);

	Notify::success(__('categories.category_success_updated'));

	return Response::redirect('admin/categories/edit/' . $id);
}));

/*
	Add
*/
Route::get('admin/categories/add', array('before' => 'auth', 'main' => function() {
	$vars['messages'] = Notify::read();
	$vars['token'] = Csrf::token();

	return View::create('categories/add', $vars)
		->partial('header', 'partials/header')
		->partial('footer', 'partials/footer');
}));

Route::post('admin/categories/add', array('before' => 'auth', 'main' => function() {
	$input = Input::get_array(array('title', 'slug', 'description'));

	$validator = new Validator($input);

	$validator->check('title')
		->is_max(3, __('categories.missing_title'));

	if($errors = $validator->errors()) {
		Input::flash();

		Notify::error($errors);

		return Response::redirect('admin/categories/add');
	}

	if(empty($input['slug'])) {
		$input['slug'] = $input['title'];
	}

	$input['slug'] = Str::slug($input['slug']);

	Category::create($input);

	Notify::success(__('categories.category_success_created'));

	return Response::redirect('admin/categories');
}));
