<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'public' => array(
		'views' => array(
			'403' => 'errors/403',			
			'404' => 'errors/404',
			'500' => 'errors/500',
			'503' => 'errors/503',
		)
	),
	'admin' => array(
		'views' => array(
			'403' => 'errors/admin_403',						
			'404' => 'errors/admin_404',
			'500' => 'errors/admin_500',
			'503' => 'errors/admin_503',
		),
	),
);
