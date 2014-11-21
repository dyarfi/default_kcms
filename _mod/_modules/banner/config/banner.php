<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('Banner',
									'BannerFile');

$config['upload_max_size']	= '2M';
$config['upload_path']		= DOCROOT.'uploads/banner/';
$config['upload_url']		= 'uploads/banner/';

$config['bannerfile_upload_max_size']	= '2M';
$config['bannerfile_upload_path']		= DOCROOT.'uploads/banner_files/';
$config['bannerfile_upload_url']		= 'uploads/banner_files/';

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
		'239x59',
		'1024x406'
    ),
	'crop'	=> array(
			array('239x59','1024x406'),
				'center'
			)
);

$config['banner_fields']		= array('show_owner'	=> FALSE,
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
// Banner position
$config['position']			= array('Middle'=>'middle');

// Module name initialize
$config['module_name']		= 'banner';

$config['module_menu']		= array(
									'banner/index'			=> 'Banner Listings',
									);

$config['module_function']	= array(
									'banner/add'			=> 'Add New Banner',
									'banner/view'			=> 'View Banner Details',
									'banner/edit'			=> 'Edit Banner Details',
									'banner/delete'		=> 'Delete Banner',
									'banner/change'		=> 'Update Banner Status'
									);
									
return array_merge_recursive (
	$config
);									
