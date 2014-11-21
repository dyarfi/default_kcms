<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	Kohana::DEVELOPMENT => array
	(
		'default' => array(
			'driver'     => 'smtp',
			'hostname'   => 'localhost',
			'username'   => 'dyarfi@localhost',
			'password'   => ''
		)
	),
	Kohana::PRODUCTION  => array
	(
		'default' => array(
			'driver'     => 'smtp',
			'hostname'   => 'smtp.'.$_SERVER['HTTP_HOST'],
			'username'   => '',
			'password'   => ''
		)
	),
);
