<?php

class Category extends Record {

	public static $table = 'categories';

	public static function slug($str) {
		return static::where('slug', '=', $str)->fetch();
	}

	public static function dropdown() {
		$options = array();

		foreach(static::all() as $itm) {
			$options[$itm->id] = $itm->title;
		}

		return $options;
	}

	public static function all() {
		$sql = '
			select categories.*, coalesce(count(discussions.id), 0) as posts
			from categories
			left join discussions on (discussions.category = categories.id)
			group by discussions.category
		';

		list($result, $statement) = DB::ask($sql);

		return $statement->fetchAll(PDO::FETCH_OBJ);
	}

}