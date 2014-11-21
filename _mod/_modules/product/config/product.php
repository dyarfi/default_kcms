<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']			= array('Product',
									'ProductFile',
									'ProductCategory',
									'ProductCategoryFile');

$config['upload_max_size']	= '2M';
$config['upload_path']		= DOCROOT.'uploads/products/';
$config['upload_url']		= 'uploads/products/';

$config['productfile_upload_max_size']	= '2M';
$config['productfile_upload_path']		= DOCROOT.'uploads/product_files/';
$config['productfile_upload_url']		= 'uploads/product_files/';

$config['category_upload_max_size']		= '2M';
$config['category_upload_path']			= DOCROOT.'uploads/productcategory_files/';
$config['category_upload_url']			= 'uploads/productcategory_files/';

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
		'540x121',
        '640x121'// New Banner
    ),
	'crop'	=> array(
			array(
				'540x121',
				'640x121'),
				'center'
			)
);

$config['productfile_fields']	= array('show_album'				=> TRUE,
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
																			)
																		)	
																	);

$config['product_fields']		= array('show_owner'	=> FALSE,
										'show_order'	=> FALSE,
										'show_description' => TRUE,
										'show_category' => TRUE,
										'show_upload'	=> TRUE,
											'uploads' => array(
												'image_1' => array(
													'label' => 'Image',
													'caption' => FALSE,
													'optional' => TRUE,
													'file_type' => 'gif,jpg,png',
													'max_file_size' => '1M',
													'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is '.$config['image']['thumbnails'][1].'px',
													'image_manipulation' => $config['image'],
												),
											)
										);

$config['category_fields'] = array(
    'show_position' => FALSE,
	'show_description' => TRUE,
	'show_enable_edit' => FALSE,
	'show_enable_delete' => TRUE,
	'show_enable_add' => TRUE,
    'show_category_upload' => FALSE,
    'uploads' => array(
        'image_1' => array(
            'label' => 'Image',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,jpg,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is '.$config['image']['thumbnails'][1].'px',
            'image_manipulation' => $config['image'],
        ),
    )
);
/**
 * Check gallery uploads field just have one field
 * and remove the other field if uploads field more than one
 ***/

//if (count($config['gallery_file_fields']['uploads']) > 1) {
	//$keys	= array_keys($config['gallery_file_fields']['uploads']);
	//$config['gallery_file_fields']['uploads']	= array($keys[0]	=> $config['gallery_file_fields']['uploads'][$keys[0]]);
//}

// Module name initialize
$config['module_name']		= 'product';

$config['module_menu']		= array(
									'productcategory/index' => 'Product Category Listings',
									'product/index'			=> 'Product Listings',
									/*'productfile/index'		=> 'File Listings',*/
									);

$config['module_function']	= array(/*
									'productfile/add'			=> 'Add New File',
									'productfile/view'			=> 'View File Details',
									'productfile/edit'			=> 'Edit File Details',
									'productfile/delete'		=> 'Delete File',
									'productfile/change'		=> 'Update File Status',
									*/
									'product/add'			=> 'Add New Product',
									'product/view'			=> 'View Product Details',
									'product/edit'			=> 'Edit Product Detauls',
									'product/delete'		=> 'Delete Product',
									'product/change'		=> 'Update Product Status',
									'productcategory/add'			=> 'Add New Product',
									'productcategory/view'			=> 'View Product Details',
									'productcategory/edit'			=> 'Edit Product Detauls',
									'productcategory/delete'		=> 'Delete Product',
									'productcategory/change'		=> 'Update Product Status',
									);
									
return array_merge_recursive (
	$config
);									
