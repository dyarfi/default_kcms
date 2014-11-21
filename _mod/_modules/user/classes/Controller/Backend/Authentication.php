<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Backend_Authentication extends Controller_Backend_BaseAdmin {
	
    protected $logged_in = TRUE;
    
    public function before() {
		// Get parent before method
        parent::before();
		
		// Check if user logged in
		if ($this->acl->user == '') {
			// Set logged in to FALSE
			$this->logged_in  = FALSE;
		}

	}
    
    public function action_index () {
		
		if($this->session->get('auth_error')) {
			// Clean auth error first
			$this->session->set('auth_error',''); 
		}	
		if($this->logged_in == TRUE) { 
			$this->redirect(str_replace('{admin_id}', $this->session->get('user_id'), Lib::config('admin.default_page'))); 
		}
		
        $this->action_validate();
		
		$this->template->page_title = Lib::config('admin.title');
		
        $this->template->content	= View::factory()->render('user/authentication_form');
		
    }
    
    public function action_validate() {
        $fields = array('email'=>'','password'=>'');
		$errors = array('');
		
		$this->session->set('auth_error', '');		
		
        if ($_POST) {
				// Validate email and password
				$post = Validation::factory($_POST)
						->rule('email', 'not_empty')
						->rule('password', 'not_empty');

				if ($post->check('admin')) {

						$request	= $post->as_array();
						
						// Module Installer default
						Model_User::instance()->install();
						Model_UserLevel::instance()->install();
						Model_UserProfile::instance()->install();
						Model_ModuleList::instance()->install();
						Model_ModelList::instance()->install();
						Model_Configuration::instance()->install();
						Model_ModulePermission::instance()->install();
						Model_UserLevelPermission::instance()->install();
						Model_UserHistory::instance()->install();

						// Authenticate User Accounts and return User ID
						$user       = new Model_User();
						$return		= $user->authenticate($request['email'], sha1($request['password']), '');
						
						// Check User Level from User ID given
						$user_level		= $user->get_user_level($return);
												
						// Check for Enable Level from User Level given
						$where_cond		= array('status'=>'enable','id'=>$user_level);
						$level_enable	= Model_UserLevel::instance()->find($where_cond,'',1);
						
						// Check for Disabled or Deleted Level and return message
						if (intval($return) && !$level_enable) {
							// Change return value
							$return = 'level_disable';
						}
						
				if (intval($return) != 0 && !empty($level_enable)) {
					$this->session->set('user_id', $return);

					// Where condition query for user level checking
					$where_cond		= array('status'=>'enable','id'=>$user_level);
					$level_enable	= Model_UserLevel::instance()->find($where_cond,'',1);

					if (intval($user_level) != 0) {
							$this->session->set('level_id', $user_level);

							$privillage		= Model_UserLevel::instance()->full_level_access($user_level);

							if (intval($privillage) == 1) {
									/** Install module table **/
									// $modules	= Kohana::modules();
									// print_r( $modules ); exit();
									Model_ModuleList::instance()->module_check();
									// Model_ModuleListPermission::instance()->module_function_check();
							}
							// Auth::instance()->logged_in(TRUE);								
							// $acl = new ACL();
							// $acl->user = TRUE;
					} 

					$module_list	= Model_ModuleList::instance()->get_modules($user_level);
					$function_list	= Model_UserLevelPermission::instance()->get_module_function($user_level);
					
					// Set Module List for All Access
					$this->session->set('module_list', json_encode($module_list));
					//ACL::instance()->_module_list($module_list);
					
					// Set Module List Function for All Access
					$this->session->set('module_function_list', json_encode($function_list));
					//ACL::instance()->_module_function_list($function_list);
					
					// Clean All auth_error Session
					$this->session->set('auth_error', '');
					
					if ($this->session->get('prev_url') != '') {
						// Redirect if to recent url if true
						$this->redirect($this->session->get('prev_url'));
					} else {
						// Redirect to view profile
						$this->redirect(str_replace('{admin_id}', $return, Lib::config('admin.default_page')));
					}
						
					return;
				} else {
					switch ($return) {
							case 'blocked':
								$this->session->set('auth_error', 'Your User ID has been blocked by Administrator');
								break;
							case 'inactive':
								$this->session->set('auth_error', 'Your User ID has been inactive by Administrator');
								break;							
							case 'level_disable':
								$this->session->set('auth_error', 'Your User Level has been disable by Administrator');
								break;							
							default:
								$this->session->set('auth_error', 'Invalid User Email or Password');
					}
					//$this->template->page_title = i18n::get('error_login');
					$errors = array('error_login' => i18n::get('error_login'));
				}
		} else {
					$fields		= Arr::overwrite($fields, $post->as_array());
					$errors		= $post->errors('admin');				
			}
        }
		
		$content_vars_index = array('fields' => $fields, 'errors' => $errors);

		$content			= View::factory('user/authentication_form');
		
		foreach ($content_vars_index as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->page_title = Lib::config('admin.title');
        
		$this->template->content = $content;
		
    }

    public function action_logout() {
		
		// Authenticate User Accounts and return User Data
		if (!empty($this->acl->user->id)) {
			$user       = new Model_User();
			
			$user->id = $this->acl->user->id;
			if ($user->load()) {
				$user->last_login = time();
				$user->update();
			}
			unset($user);
		}
		
		// Delete user_id Session
        $this->session->delete('user_id');
		
		// Delete user's module list
		$this->session->delete('module_list');
		
		// Delete user's module function list
		$this->session->delete('module_function_list');
		
		// Delete user's auth session list
		$this->session->delete('auth_error');
		
		// Delete user's acl session list
		$this->session->delete('acl_error');
		
		// Delete user's current url list
		$this->session->delete('curr_url');
		
		// Delete user's current menu list
		$this->session->delete('current');
		
		// Delete all Session IDs ?
		$this->session->destroy();
		
		// Redirect to admin-panel
		$this->redirect(ADMIN . 'authentication');
    }
    
} // End Admin
