<?php defined('SYSPATH') or die('No direct script access.');

$config['models']			= array('Career',
									/*'CareerFile',*/
									'CareerDivision',
									'CareerDivisionFile',
									'CareerApplicant');

$config['upload_path']		= DOCROOT.'uploads/careers/';
$config['upload_url']		= 'uploads/careers/';

$config['career_fields']	= array(
								'show_language' => false
							  );

// CV Uploads
$config['upload_path_cv']	= DOCROOT.'uploads/tmp_cv/';
$config['upload_url_cv']	= 'uploads/tmp_cv/';

// Grade config
$config['grade'] = array(
    0=>'SMA',
    1=>'D3/D1',
    2=>'S1/D4',
    3=>'S2',
    4=>'S3',
);

// Gender config
$config['gender'] = array(
    0=>'Female',
	1=>'Male'
);

// Marital config
$config['marital_status'] = array(
    0=>'Single',
	1=>'Married'
);		

// Yes / No field config
$config['yesno'] = array(
    0=>'No',
	1=>'Yes'
);

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
		'160x130',
        '960x170'
    ),
	'crop'	=> array(
			array(
				'160x130',
				'960x170'),
				'center'
			)
);

$config['careerdivision_fields'] = array(
	'show_synopsis' => FALSE,
    'show_description' => TRUE,
    'show_upload' => TRUE,
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
$config['module_name']		= 'career';

// Module Menu List
$config['module_menu']		= array('career/index'				=> 'Career Listings',
									'careerdivision/index'		=> 'Division Listings',
									'careerapplicant/index'	=> 'Applicant Listings'
									);

// Module Function List
$config['module_function']	= array(
									'career/add'				=> 'Add New Career',
									'career/view'				=> 'View Career Details',
									'career/edit'				=> 'Edit Career Details',
									'career/delete'				=> 'Delete Career',
									'career/change'				=> 'Update Career Status',
									'careerdivision/add'		=> 'Add New Division',
									'careerdivision/view'		=> 'View Division Details',
									'careerdivision/edit'		=> 'Edit Division Details',
									'careerdivision/delete'		=> 'Delete Division',
									'careerdivision/change'		=> 'Update Division Status',
									'careerapplicant/add'		=> 'Add New Applicant',
									'careerapplicant/view'		=> 'View Applicant Details',
									'careerapplicant/edit'		=> 'Edit Applicant Details',
									'careerapplicant/delete'	=> 'Delete Applicant');

// Devault view
$config['default'] = array (
            'view'=> 'career/default',
        );
		
return array_merge_recursive (
	$config
);