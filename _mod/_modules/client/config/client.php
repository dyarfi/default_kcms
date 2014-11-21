<?php defined('SYSPATH') or die('No direct script access.');

$config['models']			= array('Client',
									/*'ClientFile',*/
									'ClientCategory',
									'ClientCategoryFile');

$config['upload_path']		= DOCROOT.'uploads/clients/';
$config['upload_url']		= 'uploads/clients/';
$config['upload_max_size']	= '2M';

$config['clientcategory_upload_max_size']		= '2M';
$config['clientcategory_upload_path']			= DOCROOT.'uploads/clientcategory_files/';
$config['clientcategory_upload_url']			= 'uploads/clientcategory_files/';

$config['readable_mime'] = array(
    'image/gif',
    'image/jpg',
    'image/jpeg',
    'image/png',
    'video/x-flv',
    'video/flv',
    'audio/mpeg'
);

$config['mime_icon'] = array(
    'image/gif' => 'picture.png',
    'image/jpg' => 'picture.png',
    'image/jpeg' => 'picture.png',
    'image/png' => 'picture.png',
    'video/x-flv' => 'film.png',
    'video/flv' => 'film.png',
    'audio/mpeg' => 'sound.png'
);

$config['image'] = array(
    'ratio' => 'auto',
    'thumbnails' => array(
		'217x217',
        '640x121'// New Banner
    ),
	'crop'	=> array(
			array(
				'217x217',
				'640x121'),
				'center'
			)
);

$config['client_fields']		= array('show_owner'	=> FALSE,
										'show_order'	=> FALSE,
										'show_description' => TRUE,
										'show_category' => TRUE,
										'show_upload'	=> FALSE,
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

$config['clientcategory_fields'] = array(
	'show_synopsis' => FALSE,
    'show_description' => TRUE,
    'show_upload' => FALSE,
    'uploads' => array(
        'image_1' => array(
            'label' => 'Image',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,jpg,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][0].'px',
            'image_manipulation' => $config['image']
        )
    )
);

// Module name initialize
$config['module_name']		= 'client';

// Module Menu List
$config['module_menu']		= array('client/index'				=> 'Client Listings',
									'clientcategory/index'		=> 'Category Listings',
									);

// Module Function List
$config['module_function']	= array(
									'client/add'				=> 'Add New Client',
									'client/view'				=> 'View Client Details',
									'client/edit'				=> 'Edit Client Details',
									'client/delete'				=> 'Delete Client',
									'client/change'				=> 'Update Client Status',
									'clientcategory/add'		=> 'Add New Category',
									'clientcategory/view'		=> 'View Category Details',
									'clientcategory/edit'		=> 'Edit Category Details',
									'clientcategory/delete'		=> 'Delete Category',
									'clientcategory/change'		=> 'Update Category Status');
// Devault view
$config['default'] = array (
            'view'=> 'client/default',
        );
		
return array_merge_recursive (
	$config
);