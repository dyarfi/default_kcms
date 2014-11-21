<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Themes_DefaultMaintenance extends Controller_Template {

	public $auto_render = TRUE;
	public $template = 'themes/defaultmaintenance';
	public $is_mobile = '';
	public $is_maintenance = FALSE;
	public $member;
	public $data;

	/**
		* Initialize properties before running the controller methods (actions),
		* so they are available to our action.
	**/

	public function before() {
		
		/** Load Session Class **/
		$this->session		= Session::instance('native');					
		
		/** Set Initialize Variable **/
		$maintenance_mode	= Model_Configuration::instance()->load('maintenance');
		
		/** SET MAINTENANCE MODE template **/
		if (!empty($maintenance_mode->value)) {
			$this->is_maintenance = TRUE;
		}		

		/** SET IS MOBILE MODE template **/
		if (Request::$current->user_agent('mobile') && Session_Cookie::instance()->get('is_mobile') != '') {
			// Set Session cookies is_mobile to client
			Session_Cookie::instance()->set('is_mobile', 1);
			// Set is_mobile object to true
			$this->is_mobile	= TRUE;
			// Set template for mobile
			$this->template		= 'themes/defaultmobile';
		}
	
		/** Checking ajax requests **/
		if ($this->request->is_ajax()) {
			
			$this->auto_render = FALSE;
            $this->template    = 'ajaxdefault';
				
		} else {

			// Run anything that need ot run before this after checking ajax requested
			parent::before();
			$this->data['page_title']			= i18n::get('page_title') != 'page_title' ? i18n::get('page_title') : 'Welcome to Kohana';
			$this->data['meta_keywords']		= '';
			$this->data['meta_description']		= '';
			$this->data['meta_copyright']		= i18n::get('meta_copyright') != 'meta_copyright' ? i18n::get('meta_copyright') : 'Kohana Developer Team';
			
			// Set Site Counter
			if (!Session_Cookie::instance()->get('session')) Model_Setting::instance()->count_visitor();
			
			// Set Email Info 
			$email_admin= Model_Setting::instance()->load_by_parameter('email_info');
			$this->data['email_info']		= Lib::_trim_strip(@$email_admin->value);	
			
			// Set Site Counter 
			$counter = Model_Setting::instance()->load_by_parameter('counter');
			$this->data['counter']			= Lib::_trim_strip(@$counter->value);	
			
			// Set Site Title Name
			$titlename = Model_Setting::instance()->load_by_parameter('title_name');
			$this->data['title_name']		= Lib::_trim_strip(@$titlename->value);
			
			// Set Site Title Default
			$titledefault = Model_Setting::instance()->load_by_parameter('title_default');
			$this->data['title_default']	= Lib::_trim_strip(@$titledefault->value);
			
			// Set Site Quote Default
			$sitequote = Model_Setting::instance()->load_by_parameter('site_quote');
			$this->data['site_quote']		= Lib::_trim_strip(@$sitequote->value);
			
			// Set Site Copyright
			$copyright = Model_Setting::instance()->load_by_parameter('copyright');
			$this->data['copyright']		= Lib::_trim_strip(@$copyright->value);	
			
			// Set Site Copyright
			$registered = Model_Setting::instance()->load_by_parameter('registered_mark');
			$this->data['registered']		= Lib::_trim_strip(@$registered->value);
					
			// Set initialize site counter
			Model_Setting::instance()->counter();
			
			// Social media data taken from setting table 
			$where_cond   = array('status'=>'publish', 'parameter LIKE' => '%socmed_%');
			$social_media =	Model_Setting::instance()->find($where_cond);
			$buffers = array();
			foreach ($social_media as $socmed) {
				$socmed->value = strip_tags($socmed->value);
				$buffers[$socmed->parameter] = $socmed;
			}
			//--- Menu Social Media In Header home center ---//
			$this->data						 = array_merge($this->data,$buffers);
		
			// Define all css and js loads in site/config/site.php
			$config = 'site';
			
			// Define defaults css and js load
			// Add defaults styles and scripts to template variables.
			$this->data['styles']	= array_reverse(Lib::config($config.'.css'));
			$this->data['scripts']	= array_reverse(Lib::config($config.'.js'));

			//--- Set data (array) to be sent into view template ---//
			foreach ($this->data as $var => $val) {
				$this->template->$var	= $val;
			}

			unset($buffers);

		}

	}

	/* 
		* Fill in default values for our properties before rendering the output.
	*/

	public function after() {
		
		if ($this->request->is_ajax()) {
			$this->template->render();
			exit;
		} else {

			if($this->auto_render) :

				/*** Define defaults css and js load ***/
				// Define all css and js loads in site/config/site.php
				$config = 'maintenance_site';
				if (Request::$current->user_agent('mobile') && $this->is_mobile == TRUE) {
					// Define all css and js loads in site/config/mobile.php
					$config = 'maintenance_mobile';
				}		
						
				$styles		= Lib::config($config.'.css');
				$scripts	= Lib::config($config.'.js');

				// Add defaults to template variables.
				$this->template->styles  = array_reverse(array_merge($this->template->styles, $styles));
				$this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));

			endif;

		}

		// Run to clean sessions, anything that needs to run after this.
		
		//Session::instance()->set('flash','');
		//Session::instance()->set('result','');
		//Session::instance()->set('register_info','');
		//Session::instance()->set('auth_error','');
		
		// Run anything that needs to run after this.
		parent::after();

	}

}

