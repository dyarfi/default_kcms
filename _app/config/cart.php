<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'native' => array(
		// Change this if this is a new project!!
		/*** Important ***/
		'name' => sha1(COMPANY_NAME),
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
