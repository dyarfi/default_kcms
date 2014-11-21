<?php defined('SYSPATH') or die('No direct script access.');

// Module name initialize
$config['module_name']		= 'news';

// Model List
$config['models']		= array('News','NewsFile');

// Module Menu List
$config['module_menu']	= array(
							'news/index'          => 'News Listings',
						);

// Module Function
$config['module_function']	= array(
                                        // 'news/index'  => 'News Listings',
                                        'news/add'	=> 'Add New News',
                                        'news/view'	=> 'View News Details',
                                        'news/edit'	=> 'Edit News Details',
                                        'news/delete' => 'Delete News',
										'news/change' => 'Update News Status',
                                    );

$config['upload_path'] = DOCROOT.'uploads/news/';
$config['upload_url']  = 'uploads/news/';

// Readable mime Properties
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

// Image Thumbs and Crop Properties
$config['image'] = array(
			'ratio' => 'auto',
			'thumbnails' => array(
				'380x290',
				'460x370'
			),
			'crop'	=> array(
					array(
							'380x290',
							'460x370'
						),
					'center',
					)
		);

// News Field in form
$config['news_fields'] = array(
			'show_synopsis' => TRUE,
			'show_upload' => TRUE,
			'uploads' => array(
				'image_1' => array(
					'label' => 'Image 1',
					'caption' => FALSE,
					'optional' => TRUE,
					'file_type' => 'gif,jpg,png',
					'max_file_size' => '1M',
					'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
					'image_manipulation' =>$config['image'],
				),
			)
		);

// Default Views
$config['default'] = array (
            'view'=> 'news/default',
        );

return array_merge_recursive (
	$config
);