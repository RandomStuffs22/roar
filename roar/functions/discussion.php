<?php

function discussions() {
	$items = Registry::get('discussions');

	if($item = $items->valid()) {
		// register single post
		Registry::set('discussion', $items->current());

		// register category
		Registry::set('category', Category::find($items->current()->category));

		// move to next
		$items->next();
	}

	return $item;
}

function discussion_id() {
	return Registry::prop('discussion', 'id');
}

function discussion_votes() {
	return Registry::prop('discussion', 'votes');
}

function discussion_vote_url() {
	return uri_to('discussion/' . discussion_id() . '/vote');
}

function discussion_replies() {
	return Registry::prop('discussion', 'replies');
}

function discussion_views() {
	return Registry::prop('discussion', 'views');
}

function discussion_unread() {
	if(Auth::guest()) return false;

	$discussion = Registry::get('discussion');

	if( ! is_null($discussion->viewed)) {
		return strtotime($discussion->viewed) < strtotime($discussion->lastpost);
	}

	return true;
}

function discussion_created_by() {
	if($id = Registry::prop('discussion', 'created_by')) {
		if($user = User::find($id)) {
			return $user->username;
		}
	}

	return 'unknown';
}

function discussion_created_by_url() {
	return uri_to('profiles/' . discussion_created_by());
}

function discussion_created($format = null) {
	if($date = Registry::prop('discussion', 'created')) {
		return Date::relative($date, $format);
	}
}

function discussion_lastpost_by() {
	if($id = Registry::prop('discussion', 'lastpost_by')) {
		if($user = User::find($id)) {
			return $user->username;
		}
	}

	return 'unknown';
}

function discussion_lastpost_by_url() {
	return uri_to('profiles/' . discussion_lastpost_by());
}

function discussion_lastpost($format = null) {
	if($date = Registry::prop('discussion', 'lastpost')) {
		return Date::relative($date, $format);
	}
}

function discussion_title() {
	return Registry::prop('discussion', 'title');
}

function discussion_description() {
	return Registry::prop('discussion', 'description');
}

function discussion_slug() {
	return Registry::prop('discussion', 'slug');
}

function discussion_url() {
	return uri_to('discussion/' . discussion_slug());
}

function discussion_paging() {
	return Registry::get('paginator');
}

function discussion_create_url() {
	return uri_to('discussion/create');
}

function discussion_edit_url() {
	return uri_to('discussion/' . discussion_id() . '/edit');
}

function discussion_delete_url() {
	return uri_to('discussion/' . discussion_id() . '/delete');
}