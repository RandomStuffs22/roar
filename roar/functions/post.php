<?php

function posts() {
	$items = Registry::get('posts');

	if($item = $items->valid()) {
		// register single post
		Registry::set('post', $items->current());

		// move to next
		$items->next();
	}

	return $item;
}

function post_id() {
	return Registry::prop('post', 'id');
}

function post_title() {
	if($post = Registry::get('post')) {
		return $post->title;
	}

	if($topic = Registry::get('topic')) {
		return $topic->title;
	}
}

function post_user() {
	if($id = Registry::prop('post', 'user')) {
		if($user = User::find($id)) {
			return $user->username;
		}

	}
}

function post_user_gravatar($size = 32) {
	$email = 'none';

	if($id = Registry::prop('post', 'user')) {
		if($user = User::find($id)) {
			$email = $user->email;
		}
	}

	return 'http://www.gravatar.com/avatar/' . hash('md5', $email) . '/?s=' . $size . '&amp;d=mm';
}

function post_url() {
	$perpage = 10;
	$post = Registry::get('post');

	$count = Post::where('discussion', '=', $post->discussion)->where('id', '<', post_id())->count();
	$page = ceil(++$count / $perpage);

	return uri_to('discussion/' . $post->slug . '/' . $page . '/#post-' . post_id());
}

function post_user_url() {
	return uri_to('profiles/' . post_user());
}

function post_date($format = null) {
	return Date::relative(Registry::get('post')->date, $format);
}

function post_body() {
	$markdown = new Markdown;
	$body = $markdown->transform(Registry::get('post')->body);

	return $body;
}

function post_report_url() {
	return uri_to('post/' . post_id() . '/report');
}

function post_quote_url() {
	return '#quote-' . post_id();
}

function post_edit_url() {
	return uri_to('post/' . post_id() . '/edit');
}

function post_delete_url() {
	return uri_to('post/' . post_id() . '/delete');
}

function post_moderator() {
	if($user = Auth::user()) {
		if($user->role == 'administrator') {
			return true;
		}

		if($id = Registry::prop('post', 'user')) {
			if($author = User::find($id)) {
				return $author->id === $user->id;
			}
		}
	}
}