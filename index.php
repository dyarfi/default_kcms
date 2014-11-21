<?php

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#application
 */
$application = '_app';

/**
 * The directory in which your modules are located.
 *
 * @link http://kohanaframework.org/guide/about.install#modules
 */
$modules = '_mod';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#system
 */
$system = '_sys';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DS', DIRECTORY_SEPARATOR); // Do not change this into anything!!!
define('DOCROOT', realpath(dirname(__FILE__)).DS);

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot, for symlink'd index.php
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DS);
define('MODPATH', realpath($modules).DS);
define('SYSPATH', realpath($system).DS);

/**
 * Define your default Company Name, Site name as your modules directory. Change this if you changed the site.
 */
// Modules directory
define('APPMOD','_modules');
// Default Developer Name
define('DEVELOPER_NAME','DefaultDeveloper');
// Default Url Developer Name
define('DEVELOPER_URL','http://www.defaultdeveloper.co.id/');
// Default Company Name
define('COMPANY_NAME','Default');
// Default Site Name
define('SITE_NAME',($_SERVER['SERVER_NAME'] != 'localhost') ? $_SERVER['SERVER_NAME'] : COMPANY_NAME);

/**
 * Database setting setup hostname, database, username, password
 */
define('DB_HOST','localhost');
define('DB_NAME','defsite_default');
define('DB_USER','root');
define('DB_PASS','');

/** 
 * 
 * Hacking Mode
 * For System Purpose Only
 * 
**/
define('BS_PAGE',''); // Determine whether use index.php/ or not.
define('BS_DIR',str_replace('index.php','',$_SERVER['SCRIPT_NAME']));
define('BS_URL','http://'.$_SERVER['SERVER_NAME'].BS_DIR);
define('BS_ADDR',BS_URL.BS_PAGE);
define('ADMIN','admin-panel/'); // Default Administration URL
define('ASSETS',BS_URL.'assets/');
define('IMG',ASSETS.'images/');
define('CSS',ASSETS.'css/');
define('JS',ASSETS.'js/');
define('THM','themes/');
define('SYSCHMOD',"0755");
define('LANGUAGE','en');

/**
 * 
 * Set Environment Variables for DEVELOPMENT or PRODUCTION Server 
 * Or using .htaccess adding this line #SetEnv KOHANA_ENV "DEVELOPMENT"
 */
$_SERVER['KOHANA_ENV'] = 'DEVELOPMENT';

// Clean up the configuration vars
unset($application, $modules, $system);

if (file_exists('install'.EXT))
{
	// Load the installation check
	return include 'install'.EXT;
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

if (PHP_SAPI == 'cli') // Try and load minion
{
	class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
	set_exception_handler(array('Minion_Exception', 'handler'));

	Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{
	/**
	 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
	 * If no source is specified, the URI will be automatically detected.
	 */
	echo Request::factory(TRUE, array(), FALSE)
		->execute()
		->send_headers(TRUE)
		->body();
}
