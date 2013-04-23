<?php

Route::collection(array('before' => 'auth-user'), function() {

	/*
		Report post
	*/
	Route::get('post/(:num)/report', function($id) {
		if( ! $post = Post::find($id)) {
			return Response::error(404);
		}

		// get authed user
		$user = Auth::user();

		// report post
		if(Query::table('post_reports')->where('post', '=', $post->id)->where('user', '=', $user->id)->count()) {
			Notify::notice('Post has already been reported for moderation');
		}
		else {
			Query::table('post_reports')->insert(array(
				'post' => $post->id,
				'discussion' => $post->discussion,
				'user' => $user->id
			));

			Notify::notice('Post has been reported for moderation');
		}

		return Response::redirect($post->uri());
	});

	/*
		Edit post
	*/
	Route::get('post/(:num)/edit', function($id) {
		// fetch the user and the post
		$user = Auth::user();
		$post = Post::find($id);

		if(empty($post)) {
			return Response::error(404);
		}

		// check if user is author or admin
		if($user->id != $post->user and $user->role != 'administrator') {
			Notify::notice('You can not edit that post');

			return Response::redirect($post->uri());
		}

		return new Template('post_edit', compact('post'));
	});

	Route::post('post/(:num)/edit', function($id) {
		// fetch the user and the post
		$user = Auth::user();
		$post = Post::find($id);

		if(empty($post)) {
			return Response::error(404);
		}

		// check if user is author or admin
		if($user->id != $post->user and $user->role != 'administrator') {
			Notify::notice('You can not edit that post');

			return Response::redirect($post->uri());
		}

		$input = Input::get(array('body'));

		$validator = new Validator($input);

		$validator->check('body')
			->is_max(3, 'Please enter your post content');

		if($errors = $validator->errors()) {
			Input::flash();

			Notify::error($errors);

			return Response::redirect('post/' . $post->id . '/edit');
		}

		$post->body = $input['body'];
		$post->save();

		return Response::redirect($post->uri());
	});

	/*
		Delete post
	*/
	Route::get('post/(:num)/delete', function($id) {
		// fetch the user and the post
		$user = Auth::user();
		$post = Post::find($id);

		if(empty($post)) {
			return Response::error(404);
		}

		// check if user is author or admin
		if($user->id != $post->user and $user->role != 'administrator') {
			Notify::notice('You can not delete that post');

			return Response::redirect($post->uri());
		}

		return new Template('post_delete', compact('post'));
	});

	Route::post('post/(:num)/delete', function($id) {
		// fetch the user and the post
		$user = Auth::user();
		$post = Post::find($id);
		$discussion = Discussion::find($post->discussion);

		if(empty($post)) {
			return Response::error(404);
		}

		// check if user is author or admin
		if($user->id != $post->user and $user->role != 'administrator') {
			Notify::notice('You can not delete that post');

			return Response::redirect($post->uri());
		}

		$post->delete();

		if(Post::where('discussion', '=', $post->discussion)->count() == 0) {
			$discussion->delete();

			return Response::redirect('discussions');
		}

		return Response::redirect($discussion->uri());
	});

});