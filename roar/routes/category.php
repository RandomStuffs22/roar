<?php

/*
	View a category and paginate through them
*/
Route::get(array('category/(:any)', 'category/(:any)/(:num)'), function($slug, $page = 1) {

	// make sure it exists
	if( ! $category = Category::slug($slug)) {
		return Response::error(404);
	}

	// @todo: move into settings
	$perpage = 10;

	$categories = Category::all();
	$count = Query::table(Discussion::$table)->where('category', '=', $category->id)->count();
	$discussions = Discussion::by_category($category->id, ($page - 1) * $perpage, $perpage);
	$uri = Uri::to($category->slug);

	$paginator = new Paginator($discussions, $count, $page, $perpage, $uri);

	Registry::set('discussions', new Items($paginator->results));
	Registry::set('paginator', $paginator->links());

	Registry::set('categories', new Items($categories));
	Registry::set('category', $category);

	return new Template('category');
});