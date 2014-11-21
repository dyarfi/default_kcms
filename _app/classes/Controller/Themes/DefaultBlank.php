<?php defined('SYSPATH') or die('No direct script access.');class Controller_Themes_DefaultBlank extends Controller_Template {		public $auto_render = TRUE;	public $template = 'themes/defaultblank';	public $is_mobile = '';	public $member;	public $data;		/**		* Initialize properties before running the controller methods (actions),		* so they are available to our action.	**/		public function before() {				// Load Access Controller List Class		//$this->acl			= ACL::instance();		$this->session		= Session::instance();		/** Get User Id Session **/		if ($this->session->get('member_id') !== '') {			/** Get Member Id **/			$member_id		= ($this->session->get('member_id') != '') ? $this->session->get('member_id') : '';				/** Set User Member Data **/			$this->member	= !(empty($member_id)) ? Lib::to_object(get_object_vars(Model_User::instance()->load($member_id))) : '';		}		/** SET IS MOBILE MODE template **/		if (Request::$current->user_agent('mobile')) {				if (Session::instance()->get('is_mobile') !== '') {				// Set is_mobile object to true				$this->is_mobile	= TRUE;			}		}				// Checking ajax requests		if ($this->request->is_ajax()) {			$this->auto_render = FALSE;			$this->template    = 'ajaxdefault';		} else {			// Run anything that need ot run before this after checking ajax requested			parent::before();			$lang		= Session::instance()->get('lang', 'id');			i18n::lang("$lang");			$page_title          = i18n::get('page_title') != 'page_title' ? i18n::get('page_title') : 'Welcome to Kohana';						$meta_keywords		= '';			$meta_description	= '';			$meta_copyright		= i18n::get('meta_copyright') != 'meta_copyright' ? i18n::get('meta_copyright') : 'Kohana Developer Team';			$data				= array();			// Social media data taken from setting table 			$where_cond   = array('status'=>'publish', 'parameter LIKE' => '%socmed_%');			$social_media =	Model_Setting::instance()->find($where_cond);			$buffers = array();			foreach ($social_media as $socmed) {				$buffers[$socmed->parameter] = $socmed;			}			//--- Menu Social Media In Header home center			$data					= $buffers;			unset($buffers);			//--- Left menu data			// Article Category Lists - parent_id = 0			//$article_cat_list = Model_ArticleCategory::instance()->find(array('id !='=>1,'parent_id'=>0),array('id'=>'asc'),6);			//$data['left_menu']		= $article_cat_list;			//$data['default_menus']	= Lib::config('site.default_menus');			//$page_title          = !empty($page_title) ? $page_title : __('page_title'); 			//print_r($banner_footer); exit;			// Get Email Info 			$emailadmin= Model_Setting::instance()->load_by_parameter('email_info');			$this->data['email_info']	= Lib::_trim_strip(@$emailadmin->value);				// Get Site Counter 			$counter = Model_Setting::instance()->load_by_parameter('counter');			$this->data['counter']	= Lib::_trim_strip(@$counter->value);				// Site Title Default			$titledefault = Model_Setting::instance()->load_by_parameter('title_default');			$this->data['title_default']	= Lib::_trim_strip($titledefault->value);			// Site Quote Default			$sitequote = Model_Setting::instance()->load_by_parameter('site_quote');			$this->data['site_quote']	= Lib::_trim_strip(@$sitequote->value);			// Set Site Copyright			$copyright = Model_Setting::instance()->load_by_parameter('copyright');			$this->data['copyright']	= Lib::_trim_strip(@$copyright->value);				// Set Site Copyright			$registered = Model_Setting::instance()->load_by_parameter('registered_mark');			$this->data['registered']	= Lib::_trim_strip(@$registered->value);			// Set initialize site counter			$this->setting		= Model_Setting::instance();			$this->setting->counter();			if($this->auto_render) :				// Initialize empty values				$this->template->page_title          = $page_title;				$this->template->meta_keywords       = $meta_keywords;				$this->template->meta_description    = $meta_description;				$this->template->meta_copyright      = $meta_copyright;				$this->template->header              = '';				$this->template->content             = '';				$this->template->footer              = '';				$this->template->styles              = array();				$this->template->scripts             = array();				$this->template->data				 = $data;			endif;		}	}	/**		* Fill in default values for our properties before rendering the output.	**/	public function after() {		if ($this->request->is_ajax()) {			$this->template->render();			exit;		} else {		if($this->auto_render) :			// Define defaults css and js load			// Define all css and js loads in site/config/site.php			$config = 'blank_site';			if (Request::$current->user_agent('mobile') && $this->is_mobile == TRUE) {				// Define all css and js loads in site/config/mobile.php				$config = 'blank_mobile';			}					$styles		= Lib::config($config.'.css');			$scripts	= Lib::config($config.'.js');			// Add defaults to template variables.			$this->template->styles  = array_reverse(array_merge($this->template->styles, $styles));			$this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));		endif;		}				// Run anything that needs to run after this.		parent::after();	}}