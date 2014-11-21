<?php defined('SYSPATH') or die('No direct script access.');

class Widget extends Controller_Template  {
	
	protected static $_instance;
	
	//public $auto_render = TRUE;
	//public $template = 'themes/defaultadmin';
	
	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();
		return self::$_instance;
	}
	
	public function __construct () { }
	
	public function before() {
		parent::before();
	}
	
	public function load($views='',$data='') {
		
		if (empty($views)){
			throw new Kohana_Exception('No views available in :name',
				array(':name' => $views));
		}
		
		if (empty($data['module'])){
			throw new Kohana_Exception('No views available in :name',
				array(':name' => 'module'));
		}
		
		if (empty($data['content'])){
			throw new Kohana_Exception('No data available in :name',
				array(':name' => 'content'));
		}
		
		$content			= View::factory($data['module'].'/widget/'.$views);

		if (is_array($data['content'])) {
			foreach ($data['content'] as $var => $val) {
				$content->$var	= $val;
			}
		} else {
			$content->$data['content'];
		}
		
		return $content;
	}
	
	public function after() {
		parent::after();
	}

}

