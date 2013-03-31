<?php

function search_results() {
	$items = Registry::get('search_results');

	if($item = $items->valid()) {
		// register post
		Registry::set('post', $items->current());

		// register discussion
		$discussion = Discussion::find($items->current()->discussion);

		Registry::set('discussion', $discussion);

		// register category
		$category = Category::find($discussion->category);

		Registry::set('category', $category);

		// move to next
		$items->next();
	}

	return $item;
}

function search_has_results() {
	$items = Registry::get('search_results');

	return ($items) ? $items->length() : false;
}

function search_paging() {
	return Registry::get('paginator');
}