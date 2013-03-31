<?php

return array(
	'default' => 'mysql',

	'connections' => array(
		'sqlite' => array(
			'driver' => 'sqlite',
			'database' => ':memory:'
		),

		'mysql' => array(
			'driver' => 'mysql',
			'hostname' => 'localhost',
			'port' => 3306,
			'username' => 'root',
			'password' => 'bottle',
			'database' => 'roar',
			'charset' => 'utf8'
		)
	)
);