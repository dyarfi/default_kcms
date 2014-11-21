<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('Testimonial',
									'TestimonialFile');

$config['upload_max_size']	= '2M';
$config['upload_path']		= DOCROOT.'uploads/testimonial/';
$config['upload_url']		= 'uploads/testimonial/';

$config['testimonialfile_upload_max_size']	= '2M';
$config['testimonialfile_upload_path']		= DOCROOT.'uploads/testimonial_files/';
$config['testimonialfile_upload_url']		= 'uploads/testimonial_files/';

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
		'195x170',
		'300x183'
    ),
	'crop'	=> array(
			array('195x170','300x183'),
				'center'
			)
);

$config['testimonial_fields']		= array('show_owner'	=> FALSE,
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
$config['module_name']		= 'Testimonial';

$config['module_menu']		= array(
									'testimonial/index'			=> 'Testimonial',
									);

$config['module_function']	= array(
									'testimonial/add'			=> 'Add New Testimonial',
									'testimonial/view'			=> 'View Testimonial Details',
									'testimonial/edit'			=> 'Edit Testimonial Details',
									'testimonial/delete'		=> 'Delete Testimonial',
									'testimonial/change'		=> 'Update Testimonial Status'
									);
									
return array_merge_recursive (
	$config
);									
