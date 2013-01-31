<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo __('common.manage', 'Manage'); ?> <?php echo Config::get('meta.sitename'); ?></title>

		<link rel="stylesheet" href="<?php echo admin_asset('css/admin.css'); ?>">
		<link rel="stylesheet" media="(max-width: 980px), (max-device-width: 480px)" href="<?php echo admin_asset('css/small.css'); ?>">

		<meta http-equiv="X-UA-Compatible" content="chrome=1">
		<meta name="viewport" content="width=600">
	</head>
	<body class="<?php echo Auth::guest() ? 'login' : 'admin'; ?>">

		<header id="top">
			<div class="wrap">
				<?php if(Auth::user()): ?>
				<nav>
					<ul>
						<li id="logo">
							<a href="<?php echo base_url('admin'); ?>">
								<img src="<?php echo admin_asset('img/logo.png'); ?>" alt="Anchor CMS">
							</a>
						</li>
						<?php foreach(array('dashboard', 'discussions', 'categories', 'users') as $url): ?>
						<li <?php if(strpos(Uri::current(), $url) !== false) echo 'class="active"'; ?>>
							<a href="<?php echo url('admin/' . $url); ?>"><?php echo ucfirst(__('common.' . $url, $url)); ?></a>
						</li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<a class="btn" href="<?php echo url('logout'); ?>"><?php echo __('common.logout', 'Logout'); ?></a>
				<a class="btn" href="<?php echo base_url(); ?>"><?php echo __('common.visit_your_site', 'Visit your site'); ?></a>

				<?php else: ?>
				<a class="login" id="logo" href="<?php echo url('dashboard'); ?>">
					<img src="<?php echo admin_asset('img/logo.png'); ?>" alt="Anchor CMS">
				</a>
				<?php endif; ?>
			</div>
		</header>
