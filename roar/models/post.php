<?php

class Post extends Record {

	public static $table = 'posts';

	public function uri() {
		$perpage = 10;

		$discussion = Discussion::find($this->discussion);
		$count = static::where('discussion', '=', $this->discussion)->where('id', '<', $this->id)->count();
		$page = ceil(++$count / $perpage);

		return Uri::to('discussion/' . $discussion->slug . '/' . $page . '/#post-' . $this->id);
	}

	public function delete() {
		Query::table('post_reports')->where('post', '=', $this->id)->delete();

		parent::delete();

		$discussion = Discussion::find($this->discussion);
		$discussion->replies = static::where('discussion', '=', $this->discussion)->count();

		$last = $this->lastest($discussion->id);
		$discussion->lastpost_by = $last->user;
		$discussion->lastpost = $last->date;

		$discussion->save();
	}

	public function lastest($discussion) {
		return Post::where('discussion', '=', $discussion)->sort('id', 'desc')->take(1)->fetch();
	}

	public function is_moderator() {
		if($user = Auth::user()) {
			if($user->role == 'administrator') {
				return true;
			}

			return $this->user == $user->id;
		}

		return false;
	}

}