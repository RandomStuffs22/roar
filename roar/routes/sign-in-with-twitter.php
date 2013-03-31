<?php

/*
	Login with twitter
*/
Route::get('sign-in-with-twitter', function() {

	$api = get_twitter_api();

	$callback = Uri::full('callback');

	$token = $api->request_token($callback);

	if($token === false) {
		Notify::error($api->error());

		return Response::redirect('discussions');
	}

	Session::put('request_tokens', $token);

	$api->set_token($token);

	return Response::redirect($api->authorize_url());
});

Route::get('callback', function() {
	// user declined
	if(Input::get('denied')) {
		Notify::error('Login declined');

		return Response::redirect('/');
	}

	$api = get_twitter_api();

	$api->set_token(Session::get('request_tokens'));

	$token = $api->access_token(Input::get('oauth_token'), Input::get('oauth_verifier'));

	if($token === false) {
		Notify::error($api->error());

		return Response::redirect('discussions');
	}

	$api->set_token($token);

	// setup users account
	$response = $api->get('1/account/verify_credentials.json');

	if($response === false) {
		Notify::error($api->error());

		return Response::redirect('/');
	}

	$account = json_decode($response);

	$user = User::search(array('username' => $account->screen_name));

	if($user) {
		User::update($user->id, array(
			'token' => $token['oauth_token'],
			'secret' => $token['oauth_token_secret']
		));

		$user = User::find($user->id);
	}
	else {
		$user = User::create(array(
			'role' => 'user',
			'registered' => gmdate('Y-m-d H:i:s'),
			'name' => $account->name,
			'username' => $account->screen_name,
			'password' => '',
			'token' => $token['oauth_token'],
			'secret' => $token['oauth_token_secret']
		));
	}

	Session::erase('request_tokens');

	Session::put(Auth::$session, $user->id);

	return Response::redirect('discussions');
});