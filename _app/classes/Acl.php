<?php defined('SYSPATH') or die('No direct script access.');
class ACL extends Controller {
	
	protected static $_instance;
	
	public $user;
	public $privileges;
	
	public $module_list;
	public $module_function_list;
	public $module_request;
	public $module_menu;
	public $previous_url;
	
	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();
		return self::$_instance;
	}
	
	public function __construct () {
		
		$this->user	= FALSE;
		$this->now  = time();
		
		// ------- If User is Login set available data --- start
		if (Session::instance()->get('user_id') != '') {
			$this->user					= Model_User::instance()->load(Session::instance()->get('user_id'));
			$this->userhistory			= Model_UserHistory::instance();
			$this->module_list			= json_decode(Session::instance()->get('module_list'),TRUE);
			$this->module_function_list = json_decode(Session::instance()->get('module_function_list'),TRUE);
			$this->module_request		= Request::$current->controller() .'/'. Request::$current->action();	
			$this->module_menu			= $this->check_module_menu($this->module_request);
		} 
		
		//print_r($this->module_list);
		//print_r($this->module_function_list);
		
		// ------- If User is Login set available data --- end
		if (strpos(Session::instance()->get('curr_url'), ADMIN) !== FALSE 
				&& Session::instance()->get('prev_url') != Session::instance()->get('curr_url')) {
			// Set Previous URL to current URL
			Session::instance()->set('prev_url', Session::instance()->get('curr_url'));
		}	else {
			// Set current URL from current url
			Session::instance()->set('curr_url', Request::$current->detect_uri());
		}
		
		// Set previous URL from previous url session
		$this->previous_url	= Session::instance()->get('prev_url');		
		if (empty($this->user) 
				&& strpos(Request::$current->detect_uri(), ADMIN) == 1 
				&& Request::$current->controller() != 'Authentication') {
			// Redirect to authentication if direct access to all classes
			$this->redirect(ADMIN . 'authentication');			
		}	
	}
	
	/**
	* Insert items into the history table
	*
	* @access	public
	* @param	array
	* @return	bool
	*/
	public function user_history ($controller='',$action='') {

		if ($this->user !== FALSE) {
			
			$current_position	= !empty($controller) ? $controller : '';	

			$history_position 	= $this->userhistory->load($this->user->id);

			$modules_position 	= Model_ModuleList::instance()->load_by_name($current_position);
			
			$class_name		= !empty($modules_position->module_name) ? $modules_position->module_name : $current_position;		
			if (empty($history_position)) {
				$params = array(
								'module' => $class_name,
								'user_id' => @$this->user->id,
								'controller' => $controller,
								'action' => $action,
								'time' => $this->now,
								);
				$this->userhistory->add($params);
			}

			//if(!empty($history_position) && $this->user->id == $history_position->user_id) {
			if(!empty($history_position) && !empty($this->user->id)) {
				if ($controller != $history_position->controller && $controller !== 'ajaxmenu') {
					$params = array(
								'module' => $class_name,
								'user_id' => @$this->user->id,
								'controller' => $controller,
								'action' => $action,
								'time' => $this->now,
								);

					$this->userhistory->add($params);
				}
			}
		}
	}

	/**
	* Checking by user_level and bypass alert betwenn uri and controller access 
	*
	* @access	public
	* @param	array
	* @return	bool
	*/
	public function check_module_permission ($controller='',$action='') {
		
		$disable_accessible 	= FALSE;
		
		$module_list 			= $this->module_list;

		$module_function_list 	= $this->module_function_list;
		
		if ($module_list && $module_function_list && strstr(Request::$current->detect_uri(), ADMIN) !='') {
		
			if ($controller != '' && $action != '') $url_to_match = $controller .'/'. $action;	

			$function_modules 	= array_merge_recursive($module_list,$module_function_list);

			// Define all accessible function action into TRUE
			foreach ($function_modules as $modules) {

				if (!empty($modules[$url_to_match])) {

					$disable_accessible = TRUE;					

				}
			}	

			// Define controller or post that don't have to be checked
			if ($disable_accessible === FALSE 
					// For Bypassing admin-panel check_title method in all classes
					&& $action != 'check_title'
					// For Bypassing admin-panel lookup method in all classes
					&& $action != 'lookup'
					// For Bypassing admin-panel ordering method in all classes
					&& $action != 'order'
					// For Bypassing admin-panel download method in all classes
					&& $action != 'download'
					// For Bypassing authentication controller in @admin-panel/authentication
					&& $controller != 'authentication'
					// For Bypassing redirect in each @controller provides
					&& $controller != 'baseadmin') {
				
				if (Request::$current->is_ajax()) {
					// Send permission message to client
					echo 'You do not have permission to '.__($action);
					exit();
				}
				
				/* 
				 * Send permission message to client via session
				 * Set session 'acl_error' if action not accessible for users
				 */
				//print_r($controller);
				//exit;
				Session::instance()->set('acl_error', 'You do not have permission to '. __($action).'');
				$this->redirect(ADMIN . $controller);

			} 

		} 

	}
	
	/**
	* Checking and load the current controller module menu name
	*
	* @access	public
	* @param	array
	* @return	string
	*/
	public function check_module_menu ($module_menu = '') {
		
		if (empty($module_menu)) {
			return;
		}
			
		$result = (Model_ModuleList::instance()->load_by_link($module_menu) != '') 
				? @Model_ModuleList::instance()->load_by_link($module_menu)->module_name
					: @Model_ModulePermission::instance()->load_by_link($module_menu)->module_name;	
		
		return $result;
		
	}
	
	/**
	* Load the current users available module list
	*
	* @access	public
	* @param	array
	* @return	array
	*/	
	public function admin_system_modules () {

		if ($this->user === FALSE) {
			return array();
		}	

		$modules				= array();

		// Check admin url
		if (strpos(Request::$current->detect_uri(),ADMIN) !== FALSE) {	
			// Get module listings
			$modules	= $this->module_list;
		}

		return $modules;		
	}

}

