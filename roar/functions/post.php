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

	return '[deleted]';
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
	$post = Registry::get('post');

	return uri_to($post->uri());
}

function post_user_url() {
	return uri_to('profiles/' . post_user());
}

function post_date($format = null) {
	$date = Registry::prop('post', 'date');

	return Date::relative($date, $format);
}

function post_body() {
	$markdown = new Markdown;
	$body = Registry::prop('post', 'body');

	return $markdown->transform($body);
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
	if($post = Registry::get('post')) {
		return $post->is_moderator();
	}
}