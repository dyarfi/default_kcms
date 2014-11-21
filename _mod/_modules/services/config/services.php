<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('Services',
									'ServicesFile');

$config['upload_max_size']	= '2M';
$config['upload_path']		= DOCROOT.'uploads/services/';
$config['upload_url']		= 'uploads/services/';

$config['servicesfile_upload_max_size']	= '2M';
$config['servicesfile_upload_path']		= DOCROOT.'uploads/services_files/';
$config['servicesfile_upload_url']		= 'uploads/services_files/';

$config['readable_mime']	= array('image/gif',
									'image/jpg',
									'image/jpeg',
									'image/png',
									'video/x-flv',
									'video/flv',
									'audio/mpeg');

$config['mime_icon']		= array('image/gif'		=> 'picture.png',
									'image/jpg'		=> 'picture.png',
									'image/jpeg'	=> 'picture.png',
									'image/png'		=> 'picture.png',
									'video/x-flv'	=> 'film.png',
									'video/flv'		=> 'film.png',
									'audio/mpeg'	=> 'sound.png');

$config['image'] = array(
    'ratio' => 'auto',
    'thumbnails' => array(
		'230x172',
        '460x172'
    ),
	'crop'	=> array(
			array(
				'230x172',
				'460x172'),
				'center'
			)
);

$config['services_fields']		= array('show_owner'	=> FALSE,
										'show_order'	=> FALSE,
										'show_description' => TRUE,
										'show_category' => TRUE,
										'show_upload'	=> TRUE,
											'uploads' => array(
												'image_1' => array(
													'label' => 'Image',
													'caption' => FALSE,
													'description' => FALSE,
													'optional' => TRUE,
													'file_type' => 'gif,jpg,png',
													'max_file_size' => '1M',
													'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is '.$config['image']['thumbnails'][1].'px',
													'image_manipulation' => $config['image'],
												)
											)
										);

// Module name initialize
$config['module_name']		= 'services';


$config['module_menu']		= array(
									'services/index'			=> 'Service Listings',
									);

$config['module_function']	= array(
									'services/add'			=> 'Add New Service',
									'services/view'			=> 'View Service Details',
									'services/edit'			=> 'Edit Service Details',
									'services/delete'		=> 'Delete Service',
									'services/change'		=> 'Update Service Status'
									);
									
return array_merge_recursive (
	$config
);									
