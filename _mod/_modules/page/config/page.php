<?php defined('SYSPATH') or die('No direct script access.');
$config['models'] = array(
	'page',
    'pageFile',
    'pageCategory',
	'pageCategoryFile'
);
$config['upload_path'] = DOCROOT.'uploads/pages/';
$config['upload_url'] = 'uploads/pages/';
$config['category_upload_path'] = DOCROOT.'uploads/page_categories/';
$config['category_upload_url'] = 'uploads/page_categories/';
$config['readable_mime'] = array(
    'image/gif',
    'image/jpg',
    'image/jpeg',
    'image/png',
    'application/x-sh'
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
		'391x273',
        '940x459'// New Banner
    ),
	'crop'	=> array(
			array(
				'391x273',
				'940x459'),
				'center'
			)
);
$config['page_fields'] = array(
    'show_category' => TRUE,
    'show_synopsis' => TRUE,
    'show_publish_date' => FALSE,
	'show_attribute' => FALSE,
    'show_unpublish_date' => FALSE,
    'show_allow_comment' => TRUE,
    'show_tags' => FALSE,
    'show_order' => FALSE,
    'show_upload' => TRUE,
    'uploads' => array(
        'image_1' => array(
            'label' => 'Thumb Image',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,jpg,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
            'image_manipulation' => $config['image']
        ),/*
		'image_2' => array(
            'label' => 'Other Image',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,jpg,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, jpg ,png. Best Resolution is up to '.$config['image']['thumbnails'][1].'px',
            'image_manipulation' => $config['image']
        )*/
    )
);
$config['pagecategory_fields'] = array(
    'show_position' => FALSE,
	'show_enable_edit' => FALSE,
	'show_enable_delete' => TRUE,
	'show_enable_add' => FALSE,
    'show_category_upload' => TRUE,
	'show_order' => FALSE,
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
// Module name initialize
$config['module_name']		= 'page';

$config['module_menu'] = array(
    'pagecategory/index' => 'Page Categories Listings',
    'page/index' => 'Page Listings'
);
$config['module_function'] = array(
    'page/add' => 'Add New Page',
    'page/view' => 'View Page Details',
    'page/edit' => 'Edit Page Details',
    'page/delete' => 'Delete Page',
	'page/change' => 'Update Page Status',
    'pagecategory/add' => 'Add New Category',
    'pagecategory/view' => 'View Category Details',
    'pagecategory/edit' => 'Edit Category Details',
    'pagecategory/delete' => 'Delete Category',
	'pagecategory/change' => 'Update Category Status',
);
$config['default'] = array (
    'view'=> 'page/default',
);
		
 return array_merge_recursive (
	$config
 );