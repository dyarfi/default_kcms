<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_BaseAdmin extends Controller_Themes_DefaultAdmin {
	
	// Timezone and language
	public $now;
	public $lang;
	
	// Controller class and action
	public $controller;
	public $action;
	
	// Request Controller and Action params
	public $id1;
	public $id2;
	public $id3;
	public $id4;
	public $id5;

	public function before() {
		/** Parent Before in DefaultAdmin **/
        parent::before();		
		
		/** Set time **/
		$this->now      = time();
		
		/** Set language **/
        $this->lang		= i18n::$lang;
		
		/** Define current Controller and Action **/
		$this->controller	= strtolower($this->request->controller());
		$this->action		= $this->request->action();
				
		/** Set Request params **/		
		$this->id1 = Request::$current->param('id1');
        $this->id2 = Request::$current->param('id2');
        $this->id3 = Request::$current->param('id3');
		$this->id4 = Request::$current->param('id4');
		$this->id5 = Request::$current->param('id5');
		
		/** Check User Action Module List Permission Based on User Level ID **/
		$this->acl->check_module_permission($this->controller, $this->action);

		/** Check User Action Module List Permission Based on User Level ID **/
		$this->acl->user_history($this->controller, $this->action);	
		
	}

    public function action_index() {
		
		if ($this->acl->user == '') {
			/** Delete available sessions **/
			$this->session->delete('level_id','module_list','module_function_list');
			
			/** Redirect to authentication **/
			$this->redirect(ADMIN . 'authentication');
        } else {
			/** Redirect to dashboards **/
			$this->redirect(str_replace('{admin_id}', $this->session->get('user_id'), Lib::config('admin.default_page')));
		}
    }

	public function after() { 
		/** Parent After in DefaultAdmin **/
		parent::after();
				
	}

} 
