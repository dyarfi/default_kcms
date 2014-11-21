<?php defined('SYSPATH') OR die('No direct access allowed.');

/***** Setup for administrator *****/

// Setting for using dashboard or not value True or False
$config['show_dashboard'] = FALSE;

// This is supposed to be redirection to edit profile view 
$config['default_page']	  = ($config['show_dashboard'] == TRUE) 
							// Redirect to user profile 
							? ADMIN . 'userdashboard/index'
							// Redirect to user dashboard
							: ADMIN . 'user/view/{admin_id}';

// Default models
$default_models = array(
						'User',
						'UserLevel',
						'UserProfile',
						'History',
						'ModulePermission',
						'Setting',
						);

// Default dashboard model
$config['dashboard_model']	= array('UserDashboard');

// Model List							
$config['models']			= ($config['show_dashboard'])	
							// Merge dashboard model with default models
							? array_merge($config['dashboard_model'], $default_models)
							// Set default models
							: $default_models;

// Default menus
$default_menus = array(
						'user/index'			=> 'User Listings',
						'userlevel/index'		=> 'User Levels Listings',
						'userhistory/index'		=> 'User History Listings',
					);

// Dashboard menu
$config['dashboard_menu']	= ($config['show_dashboard']) 
							 // Show / install dashboard menu
							  ? array('userdashboard/index'	=> 'Dashboard Panel') 
							 // Set null
							  : '';

// Module Menu List
$config['module_menu']		= (is_array($config['dashboard_menu']))
							 // Merge dashboard menu with default menus
							  ? array_merge($config['dashboard_menu'], $default_menus)
							 // Set default menus
							  : $default_menus;	
									
/* MODULE FUNCTION
 * 
 * Currently is only set to user and and setting
 * Accessed by administrators only
 */

$config['module_function']	= array(
							
									'userdashboard/add'		=> 'Add New Dashboard',
									'userdashboard/view'	=> 'View Dashboard Details',
									'userdashboard/edit'	=> 'Edit Dashboard Details',
									'userdashboard/delete'	=> 'Delete Dashboard',
	
									'user/edit'  => 'Edit User Details',
									'user/view'  => 'View User Details',
	
									'setting/view'	  => 'View Setting Details',
									'setting/edit'    => 'Edit Setting Details',
									'setting/delete'  => 'Delete Setting Details',
                                    );

// Loading Default CSS
$config['css']				= array(
									'jquery.jqplot.min.css' => 'screen', 
									'jquery.superfish.css' => 'screen',
									'jquery.alerts.css' => 'screen',
									'colorbox.css' => 'screen',
									'shadowbox/shadowbox.css' => 'screen',
									'fancybox/jquery.fancybox.css' => 'screen',
									'jquery/jquery-ui-1.9.1/jquery.ui.all.css'=>'screen',
									
									'admin.css' => 'screen',
									'helper.css' => 'screen',
	
									//---- Bootstrap -- start --
									'bootstrap/css/bootstrap-theme.min.css' => 'all',
									'bootstrap/css/bootstrap.min.css' => 'all',	
									//---- Bootstrap -- end --
									);

// Loading Default Javascript
$config['js']				= array(
									'jquery/extend/jquery.alphanumeric.js',
									'jquery/extend/jquery.autonumeric.js',
									'jquery/extend/jquery.char.counter.js',
									'jquery/extend/jquery.shadowbox.js',
	
									/*'jquery/extend/jquery.form.js',*/
									/*'jquery/extend/jquery.cookie.js',*/
	
									'jquery.jqplot.1.0.4/plugins/jqplot.donutRenderer.min.js',
									'jquery.jqplot.1.0.4/plugins/jqplot.pieRenderer.min.js',
									'jquery.jqplot.1.0.4/jquery.jqplot.min.js',
	
									'jquery.iframe-transport.js',
									'jquery.fileupload.js',
	
									/*'extend/jquery.combo.js',*/
									'ckeditor/adapters/jquery.js',
									'ckeditor/ckeditor.js',
									'tiny_mce/jquery.tinymce.js',
	
									/*'uploadify/jquery.uploadify.v2.1.4.min.js',*/
	
									'jquery/jquery-ui-1.9.1/jquery.ui.datepicker.min.js',
									/*'jquery/jquery-ui-1.9.1/jquery.effects.bounce.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.effects.core.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.ui.autocomplete.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.ui.position.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.ui.button.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.ui.draggable.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery.ui.droppable.min.js',*/
									/*'jquery/jquery-ui-1.9.1/jquery-ui.custom.min.js',*/
									'jquery/jquery-ui-1.9.1/jquery.ui.widget.min.js',									 'jquery/jquery-ui-1.9.1/jquery.ui.core.min.js',	
									'fancybox/jquery.fancybox-1.3.3.pack.js',	
									/*'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',*/
									/*'jquery.hoverintent-min.js',*/		
                                    'jwplayer/jwplayer.js',
									'jquery.popupWindow.js',
									'jquery.fancyzoom-min.js',
									'jquery.pngFix.js',
									'jquery.superfish-min.js',
									'jquery.alerts.js',
									'jquery.validate.min.js',	
									'jquery.colorbox.js',	
									//---- Bootstrap -- start --
									//'bootstrap/bootbox.min.js',									
									'bootstrap/bootstrap.min.js',
									/*'bootstrap/bootstrap-scrollspy.js',*/
									/*'library/modernizr-2.5.3.min.js',*/
									//---- Bootstrap -- end --
									'admin.js',		
									'jquery-1.8.2.min.js',
                                    );

// Default title for the admin-panel title page
$config['title']		  = 'Administration Control Panel';
$config['show_developed'] = TRUE;
$config['developer']	  = DEVELOPER_NAME;

$config['item_per_page']  = 25;
 
/** Text Formats **/
$config['date_format']		= 'd M Y H:i:s';
$config['date_hours']		= 'd/m/Y H:i:s';

/** Error Fields **/
$config['error_field_open']	 = '<div class="form_row error">';
$config['error_field_close'] = '</div>';
$config['default'] = array ('view' => 'admin/default');
	
 return array_merge_recursive (
	$config 
 );
