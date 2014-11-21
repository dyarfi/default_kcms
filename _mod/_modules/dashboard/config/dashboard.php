<?php defined('SYSPATH') or die('No direct script access.');
$config['models'] = array(
    'Dashboard',
    'DashboardFile'
);
$config['upload_path'] = DOCROOT.'uploads/dashboards/';
$config['upload_url'] = 'uploads/dashboards/';
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
        '151x114',
		'123x139',
    ),
	'crop'	=> array(
			array(
				'151x114',
				'123x139',
				),
				'center'
			)
);
$config['dashboard_fields'] = array(
    'show_biography' => TRUE,
    'show_upload' => TRUE,
    'uploads' => array(
        'image_1' => array(
            'label' => 'Image 1 Transparent',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, png. Resolution is up to '.$config['image']['thumbnails'][0].'px and the best resolution result is '.$config['image']['thumbnails'][0].'px',
            'image_manipulation' => $config['image']
        ),
		'image_2' => array(
            'label' => 'Image 2 Original',
            'caption' => FALSE,
            'optional' => TRUE,
            'file_type' => 'gif,jpg,png',
            'max_file_size' => '1M',
            'note' => 'Allowed file types are gif, jpg ,png. Resolution is up to '.$config['image']['thumbnails'][1].'px and the best resolution result is '.$config['image']['thumbnails'][1].'px',
            'image_manipulation' => $config['image']
        )
    )
);

// Module name initialize
$config['module_name']		= 'dashboard';


// Module Menu List
$config['module_menu'] = array(
    'dashboard/index' => 'Dashboard'
);

// Module Menu Function
$config['module_function'] = array(
    // 'dashboard/index' => 'Dashboard Listings',
    'dashboard/add' => 'Add New Dashboard',
    'dashboard/view' => 'View Dashboard Details',
    'dashboard/edit' => 'Edit Dashboard Details',
    'dashboard/delete' => 'Delete Dashboard'
);
// Default View
$config['default'] = array (
            'view'=> 'dashboard/default',
);
return array_merge_recursive (
	$config
);