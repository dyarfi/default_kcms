<?php 
// Model List
$config['models']			= array(
									'User',
									'UserLevel',
									'UserProfile',
									'UserLevelPermission',
									'UserDashboard',
									'ModulePermission'
									);
// Controller List
$config['controllers']		= array('User','UserLevel','UserDashboard');

// Upload PATH and URL
$config['upload_path']		= DOCROOT.'uploads/users/';
$config['upload_url']		= 'uploads/users/';

// Image manipulation Settings
$config['image']			= array('ratio'						=> 'height',
									'thumbnails'				=> array('80x80',
																		 '120x110',
																		 '200x180',
																		 '320x240'),
									'crop'						=> array(array('80x80',
																			   '120x110',
																			   '200x180',
																			   '320x240'),
																			   'center'));
// Module name initialize
$config['module_name']		= 'user';

// Module Menu List
$config['module_menu']		= array(
									'userdashboard/index'	=> 'Dashboard Panel',
									'user/index'			=> 'User Listings',
									'userlevel/index'		=> 'User Levels Listings',
									'userhistory/index'		=> 'User History Listings'
									);
									
// Module Function
$config['module_function']	= array(
									'userdashboard/add' => 'Add New Dashboard',
									'userdashboard/view' => 'View Dashboard Details',
									'userdashboard/edit' => 'Edit Dashboard Details',
									'userdashboard/delete' => 'Delete Dashboard',
	
									'user/add'		=> 'Add New User',
									'user/view'		=> 'View User Details',
									'user/edit'		=> 'Edit User Details',
									'user/delete'	=> 'Delete User',
									'user/change'	=> 'Update User Status',

									'userlevel/add'     => 'Add User Level',
									'userlevel/view'	=> 'View User Level Details',
									'userlevel/edit'	=> 'Edit User Level Details',
									'userlevel/delete'	=> 'Delete User Level',
									'userlevel/change'	=> 'Update User Level Status',
	
									'userhistory/empty'	=> 'Empty User History'
	
	/*
									'setting/view'		=> 'View Setting Details',
									'setting/edit'		=> 'Edit Setting Details',
									'setting/delete'	=> 'Delete Setting Details',
									'setting/change'	=> 'Update Setting Status'
	 */
                                    );

// Set this to true if you want to revoke all users access permission
$config['revoke']			= false;

$config['genders']			= array(
									'male'=>'Male',
									'female'=>'Female'
								);

$config['default_page']   = ADMIN . 'user/view/{admin_id}';
$config['title']		  = 'Administration Control Panel';
$config['show_developed'] = TRUE;
	
$config['item_per_page']  = 10;
	 
/** Text Formats **/
$config['date_format']		= 'd F Y H:i:s';
$config['date_hours']		= 'd/m/Y H:i:s';

/** Error Fields **/
$config['error_field_open']		= '<div class="form_row error">';
$config['error_field_close'] 	= '</div>';

 return array_merge_recursive  ( 
	$config
 );
?>
