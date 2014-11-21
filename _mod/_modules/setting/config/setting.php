<?php defined('SYSPATH') or die('No direct script access.');
// Model List
$config['models']			= array('Setting',
                                    /*'SettingFile'*/);
// Upload List
$config['upload_path']		= DOCROOT.'uploads/settings/';
$config['upload_url']		= 'uploads/settings/';

$config['readable_mime']	= array('image/gif',
									'image/jpg',
									'image/jpeg',
									'image/png',
									'video/x-flv',
									'video/flv',
									'audio/mpeg',
                                    'audio/x-ms-wma',
									'application/x-sh',
									'application/pdf',
									'application/x-download');

$config['mime_icon']		= array('image/gif'		=> 'picture.png',
									'image/jpg'		=> 'picture.png',
									'image/jpeg'	=> 'picture.png',
									'image/png'		=> 'picture.png',
									'video/x-flv'	=> 'film.png',
									'video/flv'		=> 'film.png',
									'audio/mpeg'	=> 'sound.png');

$config['image']			= array('ratio'						=> 'auto',
									'thumbnails'				=> array('230x86',
																		 '1085x290',
                                                                         '170x56'));
// Form Field List
$config['setting_fields']	= array('show_synopsis'				=> FALSE,
									'show_upload'				=> FALSE,
									'uploads'					=> array('image_1' => array('label'				=> 'File',
																							'caption'			=> TRUE,
																							'optional'			=> TRUE,
																							'file_type'			=> 'pdf,wma,mp3',
																							'max_file_size'		=> '2M',
																							'note'				=> 'Allowed file types are pdf,wma,mp3',
																							//'image_manipulation'	=> $config['image']
                                                                                            )));

$config['setting_meta_fields']	= array('show_synopsis'				=> FALSE,
									'show_upload'				=> FALSE,
									'uploads'					=> array('image_1' => array('label'				=> 'File',
																							'caption'			=> TRUE,
																							'optional'			=> TRUE,
																							'file_type'			=> 'pdf,wma,mp3',
																							'max_file_size'		=> '2M',
																							'note'				=> 'Allowed file types are pdf,wma,mp3',
																							//'image_manipulation'	=> $config['image']
                                                                                                 )));
// Maintenance mode settings
$config['maintenance_mode'] = array(0=>'No',1=>'Yes');

// Is System mode settings
$config['is_system'] = array(0=>'No',1=>'Yes');

// Module name initialize
$config['module_name']		= 'setting';

// Form Module Menu List
$config['module_menu']		= array('setting/index'				=> 'Setting Listings',
                                    /*'meta/index'					=> 'Meta SEO Listing'*/
									);

// Form Module Function List
$config['module_function']	= array(
									// 'setting/index'				=> 'Setting Listings',
									'setting/add'					=> 'Add New Setting',
									'setting/view'					=> 'View Setting Details',
									'setting/edit'					=> 'Edit Setting Details',
									'setting/delete'				=> 'Delete Setting',
									'setting/change'				=> 'Update Setting Status'
									/*
									'meta/index'				=> 'Meta Listings',
									'meta/add'					=> 'Add New Meta Record',
									'meta/view'					=> 'View Meta Record',
									'meta/edit'					=> 'Edit Meta Record',
									'meta/delete'				=> 'Delete Meta Record'
									*/);
// Default List									
$config['default'] = array (
            'view'=> 'setting/default',
        );
		
 return array_merge_recursive (
	$config
 );