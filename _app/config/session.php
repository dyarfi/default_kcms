<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'native' => array(
		// Change this if this is a new project!!
		/*** Important ***/
		'name' => sha1(BS_URL),
		/*** Important ***/
		'encrypted' => TRUE,
		'lifetime'=>'',	
	),
	/*
	'session' => array(
		'encrypted' => TRUE,
		'lifetime'=>'',
	),*/
);
