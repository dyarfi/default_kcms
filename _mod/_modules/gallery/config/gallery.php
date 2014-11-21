<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('GalleryAlbum',
									'GalleryFile');

$config['upload_max_size']	= '2M';
$config['upload_path']		= DOCROOT.'uploads/galleries/';
$config['upload_url']		= 'uploads/galleries/';

$config['galleryfile_upload_max_size']	= '2M';
$config['galleryfile_upload_path']		= DOCROOT.'uploads/gallery_files/';
$config['galleryfile_upload_url']		= 'uploads/gallery_files/';


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

$config['image']			= array('ratio'						=> 'auto',
									'thumbnails'				=> array('130x85',
																		 '287x315'),
									'crop'						=> array(array(
																				'130x85',
																			    '287x315'
																			   ),
																		 'center'));

$config['galleryfile_fields']	= array('show_album'				=> TRUE,
										'show_allow_comment'		=> FALSE,
										'show_tags'					=> FALSE,
										'show_title'				=> TRUE,
										'show_description'			=> TRUE,
										'show_upload' 				=> TRUE,
										'show_order'				=> TRUE,
										'show_filename' 			=> TRUE,
										'uploads'					=> array('image_1' => array(
																				'label' => 'Image',
																				'caption' => TRUE,
																				'optional' => TRUE,
																				'file_type' => 'gif,jpg,png',
																				'max_file_size' => '1M',
																				'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
																				'image_manipulation' => $config['image']
																			)/*,
																			'image_2' => array(
																				'label' => 'Image',
																				'caption' => TRUE,
																				'optional' => TRUE,
																				'file_type' => 'gif,jpg,png',
																				'max_file_size' => '1M',
																				'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
																				'image_manipulation' => $config['image']
																			),
																			'image_3' => array(
																				'label' => 'Image',
																				'caption' => TRUE,
																				'optional' => TRUE,
																				'file_type' => 'gif,jpg,png',
																				'max_file_size' => '1M',
																				'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
																				'image_manipulation' => $config['image']
																			),
																			'image_4' => array(
																				'label' => 'Image',
																				'caption' => TRUE,
																				'optional' => TRUE,
																				'file_type' => 'gif,jpg,png',
																				'max_file_size' => '1M',
																				'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
																				'image_manipulation' => $config['image']
																			)
																			*/
																		)	
																	);

$config['galleryalbum_fields']	= array('show_owner'				=> FALSE,
										'show_order'				=> FALSE);


/**
 * Check gallery uploads field just have one field
 * and remove the other field if uploads field more than one
 ***/

//if (count($config['gallery_file_fields']['uploads']) > 1) {
	//$keys	= array_keys($config['gallery_file_fields']['uploads']);
	//$config['gallery_file_fields']['uploads']	= array($keys[0]	=> $config['gallery_file_fields']['uploads'][$keys[0]]);
//}

// Module name initialize
$config['module_name']		= 'gallery';

$config['module_menu']		= array(
									'galleryalbum/index'		=> 'Album Listings',
									'galleryfile/index'		=> 'File Listings',
									);

$config['module_function']	= array(
									// 'galleryfile/index'		=> 'File Listings',
									'galleryfile/add'			=> 'Add New File',
									'galleryfile/view'			=> 'View File Details',
									'galleryfile/edit'			=> 'Edit File Details',
									'galleryfile/delete'		=> 'Delete File',
									'galleryfile/change'		=> 'Update File Status',
									// 'galleryalbum/index'		=> 'Album Listings',
									'galleryalbum/add'			=> 'Add New Album',
									'galleryalbum/view'			=> 'View Album Details',
									'galleryalbum/edit'			=> 'Edit Album Detauls',
									'galleryalbum/delete'		=> 'Delete Album',
									'galleryalbum/change'		=> 'Update Album Status',
									);
									
return array_merge_recursive (
	$config
);									
