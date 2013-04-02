<?php

function categories() {
	$items = Registry::get('categories');

	if($item = $items->valid()) {
		// register single post
		Registry::set('category', $items->current());

		// move to next
		$items->next();
	}

	return $item;
}

function category_id() {
	return Registry::prop('category', 'id');
}

function category_title() {
	return Registry::prop('category', 'title');
}

function category_description() {
	return Registry::prop('category', 'description');
}

function category_slug() {
	return Registry::prop('category', 'slug');
}

function category_url() {
	return uri_to('category/' . category_slug());
}

function category_post_count() {
	return Registry::prop('category', 'posts');
}

function category_paging() {
	return Registry::get('paginator');
}