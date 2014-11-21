<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => BS_URL,
    'index_file' => BS_PAGE,
	/* @see .htaccess for $_SERVER['KOHANA_ENV'] */
    'errors' => (@$_SERVER['KOHANA_ENV'] == 'DEVELOPMENT') ? TRUE : FALSE,	
	'caching' => 1,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	// 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	// 'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	// 'minion'     => MODPATH.'minion',     // CLI Tasks
	// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation


	//'auth'				=> MODPATH . 'auth',		// Basic authentication
	'database'			=> MODPATH . 'database',		// Database access
	// 'orm'				=> MODPATH . 'orm',			// Object Relationship Mapping
	// 'cache'				=> MODPATH . 'cache',		// Caching with multiple backends
	// 'codebench'			=> MODPATH . 'codebench',	// Benchmarking tool
	// 'unittest'			=> MODPATH . 'unittest',	// Unit testing
	// 'userguide'			=> MODPATH . 'userguide',	// User guide and API documentation
	// 'phpexcel'			=> MODPATH . 'phpexcel',	// Php for Excel 
	// 'profilertoolbar'	=> MODPATH . 'profilertoolbar', // Profiler toolbar
	'error'			=> MODPATH . 'error',				// Error handling files @see .htaccess for $_SERVER['KOHANA_ENV']	
	//'geoip3'		=> MODPATH . 'geoip3',
	'image'			=> MODPATH . 'image',      // Image manipulation
	'pagination'	=> MODPATH . 'pagination', // Paging of results
	'security'		=> MODPATH . 'security', // For web security using HTML Purifier      
	'captcha'		=> MODPATH . 'captcha', // For Captcha in form
	//'compress'		=> MODPATH . 'compress', // Assets Compress Module for Css and Javascript
	
	// ========== [start] == Modules App bootstraping ======= 
	'user'			=> MODPATH . APPMOD . DS . 'user',		// User Administrator, Levels, Dashboard account
    'url'			=> MODPATH . APPMOD . DS . 'url',		// Url Manager
	'page'			=> MODPATH . APPMOD . DS . 'page',		// News
	'article'		=> MODPATH . APPMOD . DS . 'article',	// Article
	'member'		=> MODPATH . APPMOD . DS . 'member',	// Members
	'media'			=> MODPATH . APPMOD . DS . 'media',		// Media
	'download'		=> MODPATH . APPMOD . DS . 'download',	// Download
	'career'		=> MODPATH . APPMOD . DS . 'career',	// Career
	
	//'links'		=> MODPATH . APPMOD . DS . 'links',		// Links		
	//'career'		=> MODPATH . APPMOD . DS . 'career',  // Career
	//'media'		=> MODPATH . APPMOD . DS . 'media',   // Media
	//'admin'		=> MODPATH . APPMOD . DS . 'admin',   // Administrator account
	//'career'		=> MODPATH . APPMOD . DS . 'career',  // Career
	//'ceo'			=> MODPATH . APPMOD . DS . 'ceo',     // Ceo
	
	'product'		=> MODPATH . APPMOD . DS . 'product', // Product
	'news'			=> MODPATH . APPMOD . DS . 'news',	  // News
	'client'		=> MODPATH . APPMOD . DS . 'client',	  // Client	
	'gallery'		=> MODPATH . APPMOD . DS . 'gallery', // Gallery
	'banner'		=> MODPATH . APPMOD . DS . 'banner', // Banner	
	'portfolio'		=> MODPATH . APPMOD . DS . 'portfolio', // Portfolio	
	'services'		=> MODPATH . APPMOD . DS . 'services', // Services	
	'setting'		=> MODPATH . APPMOD . DS . 'setting', // Website Setting
	'site'			=> MODPATH . APPMOD . DS . 'site'
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
/*
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));
*/

// Set Cookies salt
Cookie::$salt = 'Your-Salt-Goes-Here-28-08-2013';

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

/*
Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
	->defaults(array(
		'controller' => 'errorhandler'
	));
*/

/*
Route::set('errors', 'errors/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
	->defaults(array(
		'controller' => 'errors'
	));
*/


/*** admin-panel ***/

// Replace slash with space see @index.php in root for setting
$admin = str_replace('/', '', ADMIN);

Route::set($admin, $admin . '(/<controller>(/<action>(/<id1>(/<id2>(/<id3>(/<id4>))))))',
		array(
			//'directory'  => 'backend',
			//'controller' => 'Baseadmin',
			//'action'     => 'index',
			'id1' => '[A-Za-z0-9\=\_\.]++',
			'id2' => '[A-Za-z0-9\=\_\.]++',
			'id3' => '[A-Za-z0-9\=\_\.]++',
			'id4' => '[A-Za-z0-9\=\_\.]++',
			'id5' => '[A-Za-z0-9\=\_\.]++',
			))
	->defaults(array(      
		'directory'  => 'Backend',
        'controller' => 'BaseAdmin',
        'action'     => 'index',
	));

/*** admin-panel ***/

// Maintenance Mode
Route::set('maintenance', 'maintenance(/<controller>(/<action>(/<id1>(/<id2>(/<id3>(/<id4>))))))',
		array(
			'id1' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id2' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id3' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id4' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			))
	->defaults(array(
        'directory'  => 'Backend',
        'controller' => 'Maintenance',
        'action'     => 'index',
	));

/*** SEARCH ***/
Route::set('search', 'search/<id1>',
		array(
			'controller' => 'Search', 
			'action' => 'index', 
			'id1'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			)
		)
	->defaults(array(
		'controller' => 'Search',
		'action'     => 'index',
		'id1'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
	));
/*
// Controller URL redirection
Route::set('<id1>', '<id1>', 
		array(
			'controller' => 'home', 
			'action' => 'redirect', 
			'id1'	=> '[A-Za-z0-9\-\_\@\&\.]++',
			)
		)
	->defaults(array(
			'controller' => 'home', 
			'action' => 'redirect', 
			'id1'	=> '[A-Za-z0-9\-\_\@\&\.]++',
		));

// Controller 
Route::set('<controller>/<id1>', '<controller>/<id1>')
		->defaults(array(
			'controller' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'action' => 'index', 
			'id1'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		));

// Controller
Route::set('<controller>', '<controller>(/<action>(/<id1>(/<id2>(/<id3>(/<id4>)))))', 
		array(
			'controller' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++', 
			'action' => 'index',
			'id1'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id2'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id3'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			'id4'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
			)
		)
	->defaults(array(
		'controller' => '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		'action' => 'index', 
		'id1'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		'id2'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		'id3'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		'id4'	=> '[A-Za-z0-9\-\s\:\@\$\+\=\_\.]++',
		));
 * 
 */

// Default
Route::set('default', '(<controller>(/<action>(/<id1>(/<id2>(/<id3>(/<id4>))))))')
	->defaults(array(
		'controller' => 'Home',
		'action'     => 'index',
	));	
