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
			
$config['title']				= SITE_NAME;
$config['company_name']			= COMPANY_NAME;

$config['contact_email']		= 'info@webarq.com';
$config['from_contact_email']	= 'webmaster@'.SITE_NAME;

$config['email_address']            = 'noreply@pemulihan.or.id';
$config['email_contact_subject']	= 'Contact from website - '. SITE_NAME;

/** Text Formats **/

$config['date_format']				= 'd F Y H:i:s';

/** Error Fields **/

$config['error_field_open']		= '<div class="form_row error">';
$config['error_field_close']	= '</div>';

/** Member default Page **/
$config['default_page']			= URL::site();//'members/{user_id}';

/** Bitly User and AppKey **/

$config['login_bitly']		= 'o_3n0hgrfm7g';
$config['appkey_bitly']		= 'R_e93ec4a14478a69b8b9c7c1c459878de';

/** Share Twitter Via **/
$config['twit_via']			= SITE_NAME;

 return array_merge_recursive  (
	$config
 );