<?php defined('SYSPATH') or die('No direct script access.');

/** Frontend Configs **/
$config['models'] = array('Search');

$config['css'] = array(
			//'mobile/jquery.fancybox-1.3.4.css'=>'screen',
			'mobile/general.css'=>'screen',
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile-1.2.0.css'=>'screen',*/
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile.structure-1.2.0.css'=>'screen',*/
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile.theme-1.2.0.css'=>'screen'*/
		);
		
$config['js'] = array(
			'mobile/library.js',
			//'mobile/jquery.mousewheel-3.0.4.pack.js',
			//'mobile/jquery.fancybox-1.3.4.pack.js',
			/*'mobile/jquery.mobile-1.2.0/jquery.mobile-1.2.0.js',*/
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

$config['email_address']            = 'noreply@pemulihan.or.id';
$config['email_contact_subject']	= 'Contact from website - '. SITE_NAME;

/** Text Formats **/

$config['date_format']				= 'd F Y H:i:s';

/** Error Fields **/

$config['error_field_open']		= '<div class="form_row error">';
$config['error_field_close']	= '</div>';

/** Member default Page **/
$config['default_page']			= URL::site();//'members/{user_id}';

/** Site Static Menus **/
$config['default_menus']		= array(
									'home' => 'Home',
									'about' => 'About Us',
									'download' => 'Download',
									'articles' => 'Artikel',
									'media' => 'Media',
									'links' => 'Links',
									'contact' => 'Contact Us',
									'login' => 'Login',
									);

 return array_merge_recursive  (
	$config
 );