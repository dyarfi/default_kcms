<?php defined('SYSPATH') or die('No direct script access.');

/** Frontend Configs **/
$config['models'] = array('Search');

$config['css'] = array(
			'default.css' => 'screen',
			'helper.css' => 'screen',
			'bootstrap/css/bootstrap.css' => 'all',
//			'bootstrap/css/bootstrap-responsive.css' =>'all' 
		);
		
$config['js'] = array(
				'default.js',
				'bootstrap/bootbox.min.js',
				'jquery/extend/jquery.cookie.js',
				'jquery.popupWindow.js',
				'jquery.pngFix.js',
				'bootstrap/bootstrap.min.js',
				'library/modernizr-2.5.3.min.js',
				'jquery-1.8.2.min.js',
				);	
			
$config['title']				= SITE_NAME;
$config['company_name']			= COMPANY_NAME;

$config['contact_email']		= 'info@webarq.com';
$config['from_contact_email']	= '';

$config['item_per_page']		= 10;
$config['career_item_per_page']	= 4;
$config['video_item_per_page']	= 4;
$config['photo_item_per_page']	= 10;


$config['contact_email_success']	= 'Your email has been sent, thank you for contacting us.';
$config['contact_email_error']		= 'Unexpected error while sending your email, please try again later';

$config['email_address']            = 'noreply@' . $_SERVER['HTTP_HOST'];
$config['email_contact_subject']	= 'Contact dari - '. SITE_NAME;

$config['email_activation_subject']	= 'Aktivasi Akun - '. SITE_NAME;

$config['email_verification_subject']	= 'Verification Akun - '. SITE_NAME;

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

/** Site Static Menus **/
$config['default_menus']		= array('');

 return array_merge_recursive  (
	$config
 );