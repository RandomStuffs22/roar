<?php

class Discussion extends Record {

	public static $table = 'discussions';

	public static function slug($str) {
		return static::where('slug', '=', $str)->fetch();
	}

	public static function by_category($id, $offset = 0, $perpage = 10) {
		$params[':category'] = $id;

		if($user = Auth::user()) {
			$params[':user'] = $user->id;
		}
		else $params[':user'] = 0;

		$sql = 'select d.*, ud.viewed

			from discussions as d

			left join (select * from user_discussions where user_discussions.user = :user) as ud
				on (ud.discussion = d.id)

			where d.category = :category

			order by d.lastpost desc

			limit ' . $perpage . ' offset ' . $offset;

		list($result, $statement) = DB::ask($sql, $params);

		return $statement->fetchAll(PDO::FETCH_OBJ);
	}

}