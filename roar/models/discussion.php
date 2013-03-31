<?php

class Discussion extends Record {

	public static $table = 'discussions';

	public static function slug($str) {
		return static::where('slug', '=', $str)->fetch();
	}

	public static function by_category($id, $offset = 0, $perpage = 10) {
		$params = array($id);

		if($user = Auth::user()) {
			$params[] = $user->id;
		}
		else $params[] = 0;

		$sql = '
			select discussions.*, user_discussions.viewed
			from discussions
			left join user_discussions on (user_discussions.discussion = discussions.id)
			where discussions.category = ?
			and (user_discussions.user = ? or user_discussions.user is null)
			order by votes desc, lastpost desc
			limit ' . $perpage . ' offset ' . $offset;

		list($result, $statement) = DB::ask($sql, $params);

		return $statement->fetchAll(PDO::FETCH_OBJ);
	}

}