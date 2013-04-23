<?php

class Category extends Record {

	public static $table = 'categories';

	public static function slug($str) {
		return static::where('slug', '=', $str)->fetch();
	}

	public static function dropdown() {
		$options = array();

		foreach(static::get() as $itm) {
			$options[$itm->id] = $itm->title;
		}

		return $options;
	}

	public static function all() {
		$sql = '
			select categories.*, coalesce(discussions.total, 0) as posts

			from categories

			left join (
				select count(id) as total, category from discussions group by category
			) as discussions on (discussions.category = categories.id)
		';

		list($result, $statement) = DB::ask($sql);

		return $statement->fetchAll(PDO::FETCH_OBJ);
	}

}