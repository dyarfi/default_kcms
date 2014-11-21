<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['models']				= array('modulelist');
$config['controllers']			= array('modulelist');
$config['module_menu']			= array('modulelist/index' => 'Module Listing');

$config['module_function']		= array('modulelist/edit' => 'Edit Module Details');

$config['back_function']		= array('modulelist/index' => 'Module Listings'
									//'module_list/add'		=> 'Add New Module Permission',
									//'module_list/view'	=> 'View Module Permissions',
									//'module_list/edit'	=> 'Edit Module Permissions',
									//'module_list/delete'	=> 'Delete Module Permissions'
									);
return array_merge_recursive (
	$config
);