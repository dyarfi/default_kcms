<?php defined('SYSPATH') or die('No direct script access.');

/** Frontend Configs **/

$config['css'] = array(
			'general.css' => 'screen',
			'reset.css' => 'screen',
			'helper.css' => 'screen',
			'jquery.alerts.css' => 'screen',
			'jquery_ui/base/jquery.ui.all.css' => "screen",
		);
		
$config['js'] = array(
				'library.js',
				'jquery.alerts.js',
				'jquery.hoverizr.min.js',
				'jquery.min.1.7.js',
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