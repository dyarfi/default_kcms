<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']		= array(/*'User, UserProfile','MemberLog'*/);

$config['upload_path']		= DOCROOT.'uploads/members/';
$config['upload_url']		= 'uploads/members/';

//$config['default_page']		= 'member/{member_id}';

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

$config['image']			= array('ratio'						=> 'height',
									'thumbnails'				=> array('76x75',
																		 '100x150',
																		 '140x110',
																		 '230x86',
																		 '320x240'),
									'crop'						=> array(array('100x150',
																			   '30x30',
																			   '140x110',
																			   '230x86',
																			   '320x240'),
																			   'center'));

$config['member_fields']	= array('show_category'				=> TRUE,
										'show_synopsis'				=> TRUE,
										'show_publish_date'			=> TRUE,
										'show_unpublish_date'		=> TRUE,
										'show_allow_comment'		=> TRUE,
										'show_tags'					=> TRUE,
										'show_order'				=> TRUE,
										'show_upload'				=> TRUE,
										'show_levels'				=> FALSE,
										'photo'						=> array('label'				=> 'Photo',
																			 'caption'				=> TRUE,
																			 'optional'				=> TRUE,
																			 'file_type'			=> 'gif,jpg,png',
																			 'max_file_size'		=> '1M',
																			 'image_manipulation'	=> $config['image']));

// Module name initialize
$config['module_name']		= 'member';

$config['module_menu']		= array('member/index'		=> 'Member Listings'
									);

$config['module_function']	= array(
									'member/index'		=> 'Member Listings',
									'member/add'		=> 'Add Member Profile',
                                    'member/edit'		=> 'Edit Member Profile',
                                    'member/view'		=> 'View Member Profile',
									'member/change'		=> 'Update Member Status',
									'member/delete'		=> 'Delete Member',
									);



$config['genders']			= array(
									'male'=>'Male',
									'female'=>'Female'
								);

return array_merge_recursive (
	$config
);
								