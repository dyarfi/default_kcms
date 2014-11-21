<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('Portfolio',
									'PortfolioFile',
									'PortfolioFilesFile');


$config['portfolio_upload_max_size']	= '2M';
$config['portfolio_upload_path']		= DOCROOT.'uploads/portfolios/';
$config['portfolio_upload_url']		= 'uploads/portfolios/';

$config['portfoliofile_upload_max_size']	= '2M';
$config['portfoliofile_upload_path']		= DOCROOT.'uploads/portfolio_files/';
$config['portfoliofile_upload_url']		= 'uploads/portfolio_files/';

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
																		 '287x315',
																		 '940x459'),
									'crop'						=> array(array(
																				'130x85',
																			    '287x315',
																				'940x459'),
																		 'center'));

$config['portfolio_fields']	= array('show_owner'				=> FALSE,
									'show_parent'				=> FALSE,									
									'show_order'				=> FALSE,
									'show_upload' 				=> TRUE,
									'show_description'			=> TRUE,
									'show_pages' 				=> TRUE,
									'show_filename' 			=> TRUE,
									'uploads'					=> array('image_1' => array(
																				'label' => 'Cover',
																				'caption' => TRUE,
																				'optional' => TRUE,
																				'file_type' => 'gif,jpg,png',
																				'max_file_size' => '1M',
																				'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
																				'image_manipulation' => $config['image'])));

$config['portfoliofile_fields']	= array('show_album'				=> TRUE,
										'show_allow_comment'		=> FALSE,
										'show_tags'					=> FALSE,
										'show_title'				=> TRUE,
										'show_description'			=> TRUE,
										'show_upload' 				=> TRUE,
										'show_filename' 			=> TRUE,
										'uploads'					=> array('image_1' => array(
																				'label' => 'File',
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

/**
 * Check portfolio uploads field just have one field
 * and remove the other field if uploads field more than one
 ***/

//if (count($config['portfolio_file_fields']['uploads']) > 1) {
	//$keys	= array_keys($config['portfolio_file_fields']['uploads']);
	//$config['portfolio_file_fields']['uploads']	= array($keys[0]	=> $config['portfolio_file_fields']['uploads'][$keys[0]]);
//}

// Module name initialize
$config['module_name']		= 'portfolio';

$config['module_menu']		= array(
									'portfolio/index'		=> 'Portfolio Listings',
									'portfoliofile/index'		=> 'File Listings',
									);

$config['module_function']	= array(
									// 'portfoliofile/index'		=> 'File Listings',
									'portfoliofile/add'			=> 'Add New File',
									'portfoliofile/view'			=> 'View File Details',
									'portfoliofile/edit'			=> 'Edit File Details',
									'portfoliofile/delete'		=> 'Delete File',	
									'portfoliofile/upload'		=> 'Upload Files',
									'portfoliofile/change'		=> 'Update File Status',
									// 'portfolio/index'		=> 'Portfolio Listings',
									'portfolio/add'			=> 'Add New Portfolio',
									'portfolio/view'			=> 'View Portfolio Details',
									'portfolio/edit'			=> 'Edit Portfolio Detauls',
									'portfolio/delete'		=> 'Delete Portfolio',
									'portfolio/change'		=> 'Update Portfolio Status',
									);
									
return array_merge_recursive (
	$config
);									
