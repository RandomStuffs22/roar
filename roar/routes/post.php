<?php

/*
	Report post
*/
Route::get('post/(:num)/report', array('before' => 'auth-user', 'main' => function($id) {

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
		Query::table('post_reports')->insert(array('post' => $post->id, 'user' => $user->id));

		Notify::notice('Post has been reported for moderation');
	}

	$perpage = 10;

	$count = Post::where('discussion', '=', $post->discussion)->where('id', '<', $post->id)->count();
	$page = ceil(++$count / $perpage);

	$slug = Discussion::find($post->discussion)->slug;
	$uri = 'discussion/' . $slug . '/' . $page;

	return Response::redirect($uri);
}));