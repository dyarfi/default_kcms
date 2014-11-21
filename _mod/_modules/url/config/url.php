<?php defined('SYSPATH') or die('No direct script access.');


// Model List
$config['models']		= array('Url'/*,'UrlFile'*/,'UrlLog');

// Module name initialize
$config['module_name']		= 'url';

// Module Menu List
$config['module_menu']	= array(
								'url/index'      => 'Url Listings',
								'urllog/index'   => 'Url Log Listings'
							);

// Module Function
$config['module_function']	= array(
									'url/add'	=> 'Add New Url',
									'url/view'	=> 'View Url Details',
									'url/edit'	=> 'Edit Url Details',
									'url/delete' => 'Delete Url',
									'url/change' => 'Update Url Status',
									'urllog/add'	=> 'Add New Url Log',
									'urllog/view'	=> 'View Url Log Details',
									'urllog/edit'	=> 'Edit Url Log Details',
									'urllog/delete' => 'Delete Url Log',
									'urllog/change' => 'Update Url Log Status'
							);

// models that will available in dashboard
$config['show_in_dashboard'] = $config['models'];

$config['upload_path'] = DOCROOT.'uploads/url/';
$config['upload_url']  = 'uploads/url/';

// Default Views
$config['default'] = array (
            'view'=> 'url/default',
        );

return array_merge_recursive (
	$config
);