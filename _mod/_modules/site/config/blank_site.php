<?php defined('SYSPATH') or die('No direct script access.');

/** Frontend Configs **/
$config['css'] = array(
			'general.css' => 'screen',
			'reset.css' => 'screen',
			'helper.css' => 'screen',
			'colorbox.css' => 'screen',
			'jquery.alerts.css' => 'screen',
			'jquery_ui/base/jquery.ui.datepicker.css' => 'screen',
			'jquery_ui/base/jquery.ui.all.css' => "screen",
		);
		
$config['js'] = array(
				'library.js',
				'jquery.alerts.js',
				'jquery.colorbox.js',
				'jquery.hoverizr.min.js',	
				'fabric/jquery.ui.datepicker.min.js',
				'fabric/jquery.ui.core.min.js',
				'jquery.min.1.7.js',
				);	

 return array_merge_recursive  (
	$config
 );
