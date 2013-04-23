<?php

/*
	Home page and viewing of all discussions with pagination
*/
Route::get(array('/', 'discussions', 'discussions/(:num)'), function($page = 1) {
	$user = Auth::user();

	// @todo: move into settings
	$perpage = 10;

	$query = Query::table(Discussion::$table);
	$select = 'discussions.*';

	// if the user is logged in get viewed timestamps
	if($user) {
		$select = array('discussions.*', 'user_discussions.viewed');
		$join = Query::table('user_discussions')->where('user', '=', $user->id);

		$query->left_join(function() use($join) {
			return array($join, 'user_discussions');
		}, 'user_discussions.discussion', '=', 'discussions.id');
	}

	$count = $query->count();
	$results = $query->sort('lastpost', 'desc')->take($perpage)->skip(($page - 1) * $perpage)->get($select);

	$paginator = new Paginator($results, $count, $page, $perpage, Uri::to('discussions') . '/');

	Registry::set('discussions', new Items($paginator->results));
	Registry::set('paginator', $paginator->links());
	Registry::set('categories', new Items(Category::all()));

	return new Template('index');
});

/*
	View a discussion
*/
Route::get(array('discussion/(:any)', 'discussion/(:any)/(:num)'), function($slug, $page = 1) {

	if( ! $discussion = Discussion::slug_or_id($slug)) {
		return Response::error(404);
	}

	// increment view count
	$discussion->views += 1;
	$discussion->save();

	// mark discussion viewed or set the viewed date
	if($user = Auth::user()) {
		$query = Query::table('user_discussions')->where('user', '=', $user->id)->where('discussion', '=', $discussion->id);

		if($query->count()) {
			$query->update(array(
				'viewed' => Date::mysql()
			));
		}
		else {
			$query->insert(array(
				'user' => $user->id,
				'discussion' => $discussion->id,
				'viewed' => Date::mysql()
			));
		}
	}

	// paginate posts
	$perpage = 10;

	$query = Post::where('discussion', '=', $discussion->id);
	$count = $query->count();
	$posts = $query->sort('date')->take($perpage)->skip(($page - 1) * $perpage)->get();

	$url = Uri::to('discussion/' . $discussion->slug);
	$paginator = new Paginator($posts, $count, $page, $perpage, $url);

	// set data for theme functions
	Registry::set('categories', new Items(Category::all()));
	Registry::set('discussion', $discussion);
	Registry::set('category', Category::find($discussion->category));
	Registry::set('posts', new Items($paginator->results));
	Registry::set('paginator', $paginator->links());

	return new Template('discussion');
});

/*
	User authed routes
*/
Route::collection(array('before' => 'auth-user'), function() {

	/*
		Create discussion
	*/
	Route::get('discussion/create', function() {
		$vars['categories'] = Category::dropdown();

		return new Template('discussion_create', $vars);
	});

	Route::post('discussion/create', function() {
		$input = Input::get(array('category', 'title', 'description', 'post'));

		$input['slug'] = slug($input['title']);

		$validator = new Validator($input);

		$validator->add('duplicate', function($str) {
			return Discussion::where('slug', '=', $str)->count() == 0;
		});

		$validator->check('title')
			->is_max(3, 'Please enter a title');

		$validator->check('post')
			->is_max(3, 'Please enter your post content');

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('discussion/create');
		}

		$validator->check('slug')
			->is_duplicate('Discussion already exists, ' .
				Html::link('discussion/' . $input['slug'], 'View it here'));

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('discussion/create');
		}

		$now = Date::mysql();
		$user = Auth::user();

		$discussion = Discussion::create(array(
			'category' => $input['category'],
			'slug' => $input['slug'],

			'created_by' => $user->id,
			'lastpost_by' => $user->id,

			'created' => $now,
			'lastpost' => $now,

			'replies' => 1,

			'title' => $input['title'],
			'description' => $input['description']
		));

		$post = Post::create(array(
			'discussion' => $discussion->id,
			'user' => $user->id,
			'date' => $now,
			'body' => $input['post']
		));

		// increment user post count
		$user->posts += 1;
		$user->save();

		return Response::redirect($post->uri());
	});

	/*
		Post a reply
	*/
	Route::post('discussion/(:any)', function($slug) {
		if( ! $discussion = Discussion::slug($slug)) {
			return Response::error(404);
		}

		$reply = Input::get('reply');

		if(empty($reply)) {
			Notify::error('Please enter your reply');

			return Response::redirect($discussion->uri());
		}

		// get authed user
		$user = Auth::user();
		$now = Date::mysql();

		$post = Post::create(array(
			'discussion' => $discussion->id,
			'user' => $user->id,
			'date' => $now,
			'body' => $reply
		));

		// set last post info
		$discussion->lastpost_by = $user->id;
		$discussion->lastpost = $now;

		// increment reply count
		$discussion->replies += 1;

		// update discussion
		$discussion->save();

		// increment user post count
		$user->posts += 1;
		$user->save();

		// get last page
		$perpage = 10;
		$count = Post::where('discussion', '=', $discussion->id)->count();
		$page = ceil($count / $perpage);

		return Response::redirect('discussion/' . $discussion->slug . '/' . $page . '#post-' . $post->id);
	});

	/*
		Up Vote a discussion
	*/
	Route::get('discussion/(:num)/vote', function($id) {
		if( ! $discussion = Discussion::find($id)) {
			return Response::error(404);
		}

		// get authed user
		$user = Auth::user();
		$voted = Query::table('user_votes')->where('user', '=', $user->id)->where('discussion', '=', $discussion->id);

		// check if user hasnt voted
		if( ! $voted->count()) {
			// increment votes
			$discussion->votes += 1;
			$discussion->save();

			// add user to votes to stop users voting multiple times
			$voted->insert(array('user' => $user->id, 'discussion' => $discussion->id));
		}
		else {
			Notify::notice('You have already voted on this discussion');
		}

		return Response::redirect('discussion/' . $discussion->slug);
	});

});

/*
	Admin authed routes
*/
Route::collection(array('before' => 'auth'), function() {

	/*
		Edit discussion
	*/
	Route::get('discussion/(:num)/edit', function($id) {

		if( ! $discussion = Discussion::find($id)) {
			return Response::error(404);
		}

		$vars['categories'] = Category::dropdown();
		$vars['discussion'] = $discussion;

		return new Template('discussion_edit', $vars);
	});

	Route::post('discussion/(:num)/edit', function($id) {

		if( ! $discussion = Discussion::find($id)) {
			return Response::error(404);
		}

		$input = Input::get(array('category', 'title', 'description'));

		$input['slug'] = slug($input['title']);

		$validator = new Validator($input);

		$validator->add('duplicate', function($str) use($id) {
			return Discussion::where('slug', '=', $str)->where('id', '<>', $id)->count() == 0;
		});

		$validator->check('title')
			->is_max(3, 'Please enter a title');

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('discussion/' . $discussion->id . '/edit');
		}

		$validator->check('slug')
			->is_duplicate('Discussion already exists, ' .
				Html::link('discussion/' . $input['slug'], 'View it here'));

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('discussion/' . $discussion->id . '/edit');
		}

		$now = Date::mysql();
		$user = Auth::user();

		Discussion::update($discussion->id, $input);

		return Response::redirect('discussions');
	});

	/*
		Delete discussion
	*/
	Route::get('discussion/(:num)/delete', function($id) {
		if( ! $discussion = Discussion::find($id)) {
			return Response::error(404);
		}

		return new Template('discussion_delete', compact('discussion'));
	});

	Route::post('discussion/(:num)/delete', function($id) {

		if( ! $discussion = Discussion::find($id)) {
			return Response::error(404);
		}

		$discussion->delete();

		return Response::redirect('discussions');
	});

});