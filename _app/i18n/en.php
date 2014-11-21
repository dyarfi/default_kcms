<?php defined('SYSPATH') or die('No direct script access.');

return array(
    // General Page Settings
    'page_title'        => SITE_NAME,
    'meta_keywords'     => SITE_NAME,
    'meta_description'  => SITE_NAME,
    'google_analytics'  => SITE_NAME,
    'meta_copyright'    => SITE_NAME,
    'system_language'   => array('id'=>'Indonesia', 'en'=>'English'),
	'is_system'			=> 'Is System',
    
    // Label
    'language'          => 'Bahasa',
    'title'             => 'Title',
    'label'             => 'Label',
    'short_desc'        => 'Short Description',
    'full_desc'         => 'Full Description',
    'date'              => 'Date',
    'time'              => 'Time',
    'datetime'          => 'Date Time',
    'status'            => 'Status',
    'status_value'      => array(0=>'Unpublish',1=>'Publish'),
	'status_value_user' => array(0=>'Inactive',1=>'Active'),
	'default'           => 'Default',
	'default_value'     => array(0=>'No',1=>'Yes'),
	'delete_value'      => array(0=>'Undelete',1=>'Deleted'),
    'content_listing'   => '%type Listing',
    'content_detail'    => 'View %type Details',
    'content_edit'      => 'Edit %type Details',
    'content_translate' => 'Translate %type Details',
    'content_new'       => 'Add New %type',
    'changed_status'    => 'Changed Status',
    'date_format'       => 'Format : dd-mm-yyyy',
    'title_action'      => 'Click for %action this item',
    'page'              => 'Page',
    'image'             => 'Image',
	'icon'              => 'Icon',
    'url'               => 'Url',	
    'more'              => 'Read More',
	'news'				=> 'News',
	'product' 			=> 'Product',
	'promo' 			=> 'Promotion',	
	'price' 			=> 'Price',
	'channel' 			=> 'Channel',
	'distribution'		=> 'Distribution',
	'about' 			=> 'About',
	'latest'       		=> 'Latest %type',
	'back_to'      		=> 'Back to %type',
	'search'			=> 'Search',
	'language'			=> 'Language',
	'no_detail'			=> 'No %type Detail', 
	'here'				=> 'You are here',
	'home'				=> 'Home', 
	'contact'			=> 'Contact', 
	'us'				=> 'Us',
	'or'				=> 'or',
	'send_message'		=> 'Send Message',
	'no_content'		=> 'No Content', 
	
	// Member
	'login'				=> 'Login',
	'free'				=> 'Free',
	'register'			=> 'Register',
    'username'			=> 'Username',
	'fullname'			=> 'Full Name',
	'phone'				=> 'Handphone',
	'email'				=> 'Email',
	'password'			=> 'Password',
	'password2'			=> 'Confirm Password',
	'address'			=> 'Address',	
	'country'			=> 'Country',
	'church'			=> 'Church',
	'birthday'			=> 'Birthday',
	'about'				=> 'About',	
	'forgot'			=> 'Forgot',
	'forgot_password'	=> 'Please fill your email to continue',
	'gender'			=> 'Gender',	
	'confirm'			=> 'Confirm', 
	'captcha'			=> 'Captcha',
	'agree'				=> 'Agreement',
	'agreement'			=> 'I agree with the Terms and Conditions',
	'activating_acc'	=> 'Account Activation',
	'check_email'		=> 'Please check your email in %email',
	'dashboard'			=> 'Dashboard',
	'admin_panel'		=> 'Administration Control Panel',
	'update_profile'	=> 'Update Profile',
	'view_profile'		=> 'View Profile',
	'logout'			=> 'Logout',
	'message_password'	=> 'Wrong password',
	'message_email'		=> 'Email unregistered',
	'account_free'		=> 'To have an account at '.SITE_NAME.', you must complete the following data',
	'captcha_code'		=> 'Captcha',
	'captcha_reload'	=> 'Reload Captcha',
	'submit'			=> 'Submit',	
	'cancel'			=> 'Cancel',	
	
	//Search 
	'search_empty'		=> 'Search result is empty',
	
	// Admin
	'admin'				=> 'Admin',
	'menu_panel'		=> 'MODULE PANEL MENUS',
	'welcome_admin'		=> 'Selamat, datang %admin',
	'error_login'		=> 'Authentication failed',
	
	// Error encountered
	'error_enc'			=> 'Some errors were encountered, please check the details you entered.',
	
	// Contact Form
	'admin_contact'		=> '<h2>Contact us form</h2>Dear Administrator, there is someone contacting from the web<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name : %name,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Company : %company,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email : %email,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Message : %message<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							Thank you for your attention.<br/>
							<span style="font-style: italic; color: lightgray; font-size: 11px; margin: 0px">
							<b>%site_name</b><br/>%address<br/>
							Phone : %phone Fax : %fax<br/>
							<span>',
	'public_contact'	=> '<h2>Contact us form</h2>
							Dear %name, thank you for contacting us.<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							We will reply to your messages as soon as posible.<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							Thank you for your attention.<br/>
							<span style="font-style: italic; color: lightgray; font-size: 11px; margin: 0px">
							<b>%site_name</b><br/>%address<br/>
							Phone : %phone Fax : %fax<br/>
							<span>',
	'contact_success'	=> 'Your message has been sent',
	'form_contact_us'	=> 'Form Contact Us',
	
	// Contact Us
	'name'				=> 'Name',
	'company'			=> 'Company',
	'message'			=> 'Message',
	
	// Career
	'career'			=> 'Career',
	'no_career'			=> 'No vacancies at this time.',
	
	// Pagination			
	'first'				=> 'First',
	'previous'       	=> 'Previous',
	'next'				=> 'Next',
	'last'       		=> 'Last',
	
    // Warning
    'warning_delete'    => 'Ingin menghapus item ini ? Item yang dihapus tidak dapat di-restore kembali',
    
    // Error
    'error_no_data'     => 'No record found',
    'error_no_translate'        => 'No available translate yet',
    'error_no_direct_access'    => 'No direct script access allowed',
    'error_upload_file' => array (
                                '501' => 'Ups. Sistem error, tidak dapat mengunggah file',
                                '503' => 'File yang anda pilih tidak diijinkan',
                                '504' => 'Ukuran gambar yang anda pilih tidak diijinkan',
                            )
    
    
);