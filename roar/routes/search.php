<?php

/*
	Search
*/
Route::get('search', function() {
	return new Template('search');
});

Route::post('search', function() {
	// search and save search ID
	$term = filter_var(Input::get('query', ''), FILTER_SANITIZE_STRING);

	Session::put(slug($term), $term);

	return Response::redirect('search/' . slug($term));
});

/*
	View results
*/
Route::get(array('search/(:any)', 'search/(:any)/(:num)'), function($slug = '', $offset = 1) {
	$term = Session::get($slug);

	$perpage = 10;

	$query = Post::join('discussions', 'discussions.id', '=', 'posts.discussion')
		->where('posts.body', 'like', '%' . $term . '%');

	$count = $query->count();

	$posts = $query->take($perpage)
		->sort('date', 'desc')
		->skip(($offset - 1) * $perpage)
		->get(array('posts.*', 'discussions.title', 'discussions.slug'));

	$paginator = new Paginator($posts, $count, $offset, $perpage, Uri::to('search/' . $slug));

	Registry::set('search_results', new Items($paginator->results));

	Registry::set('paginator', $paginator->links());

	return new Template('search');
});