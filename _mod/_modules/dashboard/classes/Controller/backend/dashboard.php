<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Dashboard extends Controller_Backend_BaseAdmin {
    
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

    protected $dashboard;
    protected $admin;
	protected $modules;
	
	public function before () {
		// Get parent before method
        parent::before();
        
		$this->_class_name		= $this->controller;
		
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		
		//$this->statuses			= array('publish','unpublish');

    }
    
    public function action_main() { $this->template->content = 'Default Dashboard'; }
    
    public function action_index() {       
		
		$dashboard	= array();
		$dashboard_data = array();
		foreach (Kohana::modules() as $row) {
			$module_app				= strstr($row, MODPATH . APPMOD);
			$module					= str_replace(MODPATH . APPMOD, '', $module_app);
			$class_name			= str_replace(DS, '', $module);
			$config_module[]		= $class_name;
		}
		
		if(is_array($config_module) && count($config_module) > 0) {
			$dashboard = array();
			foreach($config_module as $row) {
				if($row	!= 'site' && !empty($row)) {
					if (is_file(MODPATH . APPMOD . DS . $row . DS .'config/' .$row. '.php')) {
						
						// dashboard model data
						$dashboard[$row]	= Lib::config($row.'.show_in_dashboard');

						if (!is_array($dashboard[$row]) && empty($dashboard[$row]))
							continue;

					}
				}
			}
			/*
			if (is_array($dashboard) && $dashboard > 0) {
				$class = array();
				$dashboard_data = array();
				foreach ($dashboard as $classes => $model){
					if(!empty($dashboard[$classes])) {
						if (!empty($model)) {
						//print_r($classes); exit();
						//$class = 'Model_'.ucfirst($model);
						//$dashboard_data = new $class;
						}
					}
				}
			}
						 
			print_r($dashboard);
			*/
		}
	
		// Data dashboard for url
		$sql1 = 'SELECT MAX( `clicks` ) AS `clicks`, keywords '
			  .'FROM `wus_urls` '
			  .'GROUP BY `clicks` , `keywords` '
			  .'ORDER BY `clicks` DESC '
			  .'LIMIT 0 , 10;';
		
		$url_top_ten_click = Model_Dashboard::instance()->query($sql1, TRUE);
		
		$sql2= 'SELECT COUNT(`user_agent`) as `count`, `user_agent` '
			  .'FROM `wus_url_logs` '
			  .'GROUP BY `user_agent` '
			  .'LIMIT 0 , 10;';
		
		$url_top_ten_browser = Model_Dashboard::instance()->query($sql2, TRUE);
		
		$sql3= 'SELECT COUNT(`ip_address`) AS `count`, `ip_address` '
			  .'FROM `wus_url_logs` '
			  .'GROUP BY `ip_address` '
			  .'ORDER BY `count` DESC '
			  .'LIMIT 0 , 10;';
		
		$url_top_ten_ip = Model_Dashboard::instance()->query($sql3, TRUE);
		
		//print_r($url_top_ten_click);
		//print_r($url_top_ten_browser);
		//print_r($url_top_ten_ip);
		
		/** Views **/
		$content_vars		= array(
									//'dashboard_data'	=> $dashboard_data,	
									
									'url_top_ten_click' => $url_top_ten_click,
									'url_top_ten_browser' => $url_top_ten_browser,
									'url_top_ten_ip'	=> $url_top_ten_ip,	
			
									'class_name'		=> $this->_class_name,
									'class_name'		=> $this->_class_name,
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->page_title		= Lib::config('admin.title');
		$this->template->content		= $content; 
		
	}
} // End Event
