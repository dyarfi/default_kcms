<?php defined('SYSPATH') or die('No direct script access.');

/** Frontend Configs **/
$config['css'] = array(
			'mobile/jquery.fancybox-1.3.4.css'=>'screen',
			'mobile/style.css'=>'screen',
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile-1.2.0.css'=>'screen',*/
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile.structure-1.2.0.css'=>'screen',*/
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile.theme-1.2.0.css'=>'screen'*/
		);
		
$config['js'] = array(
			'mobile/library.js',
			'mobile/jquery.mousewheel-3.0.4.pack.js',
			'mobile/jquery.fancybox-1.3.4.pack.js',
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile-1.2.0.js',*/
			'jquery-1.8.2.min.js',
		);

 return array_merge_recursive  (
	$config
 );
