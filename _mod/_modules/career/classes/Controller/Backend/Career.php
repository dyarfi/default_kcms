<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Career extends Controller_Backend_BaseAdmin {
    
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
    protected $_upload_path;
	protected $_upload_url;

	protected $career;
	protected $division;
	protected $user;
    
	public function before () {
		// Get parent before method
        parent::before();	
		
		$this->_class_name 		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
				
		$this->_search_keys		= array('subject'	=> 'Job Title',
										'sent_to'	=> 'Sent To',
										'division'	=> 'Division',
										'ref_no'	=> 'Ref No',
										'location'	=> 'Location');
		
		$this->career			= new Model_Career;
		$this->careerApp		= new Model_CareerApplicant;
		$this->division			= new Model_CareerDivision;	
		$this->user				= new Model_User;
		
		$this->divisions		= $this->division->find(array('status'=>'publish'));
		
		$users					= $this->user->find();
		
		$users_arr				= array();
		foreach ($users as $user){
			$users_arr[$user->id] = $user;
		}
		$this->_users			= $users_arr;		

		$this->statuses			= array('publish',
										'unpublish');
		
		// Data User Division		
		$user_division			= Model_User::instance()->find(array('id'=>$this->session->get('user_id')));
		
		$this->user_division	= array();
		
		// Data settings for website
		$settings = Model_Setting::instance()->find(array('status' => 1));
		$buffers  = array();
		foreach ($settings as $setting){
			$buffers[$setting->parameter] = $setting->value;
		}
		$settings = $buffers;
		
		// Object that accesed from within all inheritance
		$this->settings = $settings;
		//print_r($this->settings);	
		
		// User id from user login session 'user_id'
		$this->_uid = $this->session->get('user_id');
		
		unset($buffers,$users_arr);
        
    }
    
    public function action_main() { $this->template->content = 'Default Career'; }
    
    public function action_index() {       
				
		$where_cond	= array('id !=' => '0');
		//$this->career->find($where_cond);
		//exit();
		//$order_by	= array('added' => 'DESC');

		/** Find & Multiple change status **/
		//print_r($_POST); exit();
		if ($_POST) {
			$post	= new Validation($_POST);

			if ($this->id1 == 'select_action' && isset($_POST['check'])) {
				$rows	= $_POST['check'];
				
				foreach ($rows as $row) {
					$this->career->id	= $row;

					if (!$this->career->load())
						continue;

					$this->career->status	= $_POST['select_action'];
					$this->career->update();
				}

				$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

				$this->redirect($redirect_url);
				return;
			}

			if (isset($_POST['field']) || isset($_POST['keyword'])) {
				$post->rule('field', array($this, '_valid_search_key'), array(':validation', 'field'));
				$post->rule('keyword', 'regex', array(':value', '/^[a-z0-9_.\s\-]++$/iD'));

				if ($post->check()) {
					$where_cond[$_POST['field'] . ' LIKE']	= $_POST['keyword'] . '%';

					$filters	= array('f'	=> $_POST['field'],
										'q'	=> $_POST['keyword']);

					$this->session->set($this->_class_name.'_filter', serialize($filters));
				} else if (isset($_POST['find'])) {
					$this->session->delete($this->_class_name.'_filter');
				}
			}

			if ($this->session->get($this->_class_name.'_filter') !== FALSE) {
				$filters	= unserialize($this->session->get($this->_class_name.'_filter'));

				if (in_array($filters['f'], array_keys($this->_search_keys)) && $filters['q'] != '')
					$where_cond[$filters['f'] . ' LIKE']	= '%' . $filters['q'] . '%';
			}
		}

		/** Table sorting **/

		$params		= Request::$current->param();
		
		$sorts		= array('asc', 'desc');

		$sort		= isset($params['id2']) ? $this->id2 : 'name';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);
		
		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		//$page_index	= isset($params['no']) ? $params['no'] - 1: 0;

		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 4;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN.$this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;
		
		$table_headers	= array('name'				=> 'Job Title',
								'sent_to'			=> 'Sent To',
								'ref_no'			=> 'Ref No',
								'location'			=> 'Location',
								'start_date'		=> 'Start Date',
								'end_date'			=> 'End Date',
								/*'language'			=> 'Language',*/
								'status'			=> 'Status',
								'added'				=> 'Added',
								'modified'			=> 'Modified');

		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);

			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];

			$order_by	= array($sort	=> $order);

			$base_url	= ADMIN.$this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . $page_url;
		}

		/** Execute list query **/

		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';

		$where_cond	= isset($where_cond) ? $where_cond : '';
		
		$total_rows		= $this->career->find_count($where_cond);
		$total_record 	= $total_rows;
		
		$listings	= $this->career->find($where_cond, $order_by, $per_page, $offset);
		
		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);
			$this->redirect($base_url.$page_index);
			return;
		}

		$this->session->get($this->_class_name.'_index', $base_url.$page_index);

		/** Initialize pagination **/

		$pagination = Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 )
		);

		/** Views **/

		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'total_record'	=> $total_record,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'search_keys'	=> $this->_search_keys,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $offset,
									//'params'		=> $params,
									'total_record'  => $total_record,
									'pagination'	=> $pagination);

		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
		
        //$this->template->content = View::factory(LT.'two_column',$content);
    }
    
	public function action_view() {
		
		$career_id			= !empty($this->id1) ? $this->id1 : '';		
		$this->career->id	= $career_id;
		
		if (!$this->career->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}
		
		/** Views **/
		$content_vars		= array('career'		=> $this->career,			
									'divisions'		=> $this->divisions,
									'statuses'		=> $this->statuses,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
    }
    
    public function action_add() {       
		$fields	= array('division_id'	=> '',
						'subject'		=> '',
						'name'			=> '',
						'sent_to'		=> '',
						'ref_no'		=> '',
						'start_date'	=> '',
						'end_date'		=> '',
						'report_to'		=> '',
						'job_purpose'	=> '',
						'responsibilities'		=> '',
						'requirements'	=> '',
						'location'		=> '',
						'company'		=> '',
						'ext_link1'		=> '',
						'ext_link2'		=> '',
						//'ext_link3'		=> '',
						'status'		=> '');

		$errors	= $fields;

		if ($_POST) {
			$post	= new Validation($_POST);
			
			//$post->pre_filter('trim', 'subject', 'name');
			//$post->post_filter('ucfirst', 'subject');
			$post->rule('subject', 'not_empty');
			$post->rule('name', 'not_empty');			
			$post->rule('division_id', 'not_empty');						
			$post->rule('sent_to', 'not_empty');
			$post->rule('sent_to', 'Valid::email', array(':value'));
			//$post->rule('sent_to', array($this, '_valid_email'), array(':validation', 'email'));
					
			// Checking if link is between this tow patterns 'jobstreet' or 'jobsdb'
			$post->rule('ext_link1', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));
			$post->rule('ext_link2', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));
			//$post->rule('ext_link3', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));			
			
			
			if ($post->check()) {
				$fields	= $post->as_array();

				$params	= array('division_id'		=> $fields['division_id'],
								'subject'			=> $fields['subject'],
								'name'				=> URL::title($fields['subject']),
								'sent_to'			=> $fields['sent_to'],
								'ref_no'			=> $fields['ref_no'],
								'start_date'		=> $fields['start_date'],
								'end_date'			=> $fields['end_date'],
								'report_to'			=> $fields['report_to'],
								'job_purpose'		=> $fields['job_purpose'],
								'responsibilities'	=> $fields['responsibilities'],
								'requirements'		=> $fields['requirements'],
								'location'			=> $fields['location'],
								'company'			=> $fields['company'],
								'ext_link1'			=> $fields['ext_link1'],
								'ext_link2'			=> $fields['ext_link2'],
								//'ext_link3'			=> $fields['ext_link3'],
								'status'			=> $fields['status']);

				$id		= $this->career->add($params);

				$this->session->set('function_add', 'success');

				if (isset($_POST['add_another'])) {
					$this->redirect(ADMIN.$this->_class_name.'/add');
					return;
				}

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$id);
				return;
			} else {
				$fields		= Arr::overwrite($fields, $post->as_array());
				$errors 	= Arr::overwrite($errors, $post->errors('validation'));
				$buffers	= $errors;
				foreach ($errors as $row_key => $row_val) {
					if ($row_val != '')
						$buffers[$row_key]	= Lib::config('admin.error_field_open').ucfirst($row_val).Lib::config('admin.error_field_close');
					else
						$buffers[$row_key]	= $row_val;
				}
				$errors		= $buffers;
			}
		}
	
		
		/** Views **/
		
		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'statuses'	=> $this->statuses,
									'divisions'	=> $this->divisions,
									'settings'  => $this->settings,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
    }
    
    public function action_edit() {
		$this->career->id	= $this->id1;

		if (!$this->career->load()) {
			$this->redirect(ADMIN . 'error/invalid_request');
			return;
		}

		$fields	= array('division_id'		=> '',
						'subject'			=> '',
						'name'				=> '',
						'sent_to'			=> '',
						'ref_no'			=> '',
						'start_date'		=> '',
						'end_date'			=> '',
						'report_to'			=> '',
						'job_purpose'		=> '',
						'responsibilities'	=> '',
						'requirements'		=> '',
						'location'			=> '',
						'company'			=> '',			
						'ext_link1'			=> '',
						'ext_link2'			=> '',
						'status'			=> '');

		$errors	= $fields;

		if ($_POST) {
			$post	= new Validation($_POST);
			
			// Checking if link is between this tow patterns 'jobstreet' or 'jobsdb'
			//$post->add_callbacks('ext_link1', array($this, '_check_matches1'));
			//$post->add_callbacks('ext_link2', array($this, '_check_matches2'));
			
			$post->rule('subject', 'not_empty');
			$post->rule('name', 'not_empty');			
			$post->rule('division_id', 'not_empty');						
			$post->rule('sent_to', 'not_empty');
			$post->rule('sent_to', 'Valid::email', array(':value'));
			
			// Checking if link is between this tow patterns 'jobstreet' or 'jobsdb'			
			$post->rule('ext_link1', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));
			$post->rule('ext_link2', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));
			//$post->rule('ext_link3', 'regex', array(':value', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'));			
			
			if ($post->check()) {
				$fields		= $post->as_array();

				$params		= array('division_id'		=> !empty($fields['division_id']) ? $fields['division_id'] : '',
									'subject'			=> $fields['subject'],
									'name'				=> URL::title($fields['subject']),
									'sent_to'			=> $fields['sent_to'],
									'ref_no'			=> $fields['ref_no'],
									'start_date'		=> $fields['start_date'],
									'end_date'			=> $fields['end_date'],
									'report_to'			=> $fields['report_to'],
									'job_purpose'		=> $fields['job_purpose'],
									'responsibilities'	=> $fields['responsibilities'],
									'requirements'		=> $fields['requirements'],
									'location'			=> $fields['location'],
									'company'			=> $fields['company'],	
									'ext_link1'			=> $fields['ext_link1'],
									'ext_link2'			=> $fields['ext_link2'],
									'status'			=> $fields['status']);

				foreach ($params as $var => $val) {
					$this->career->$var	= $val;
				}

				$this->career->update();

				$this->session->set('function_update', 'success');

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->career->id);
				return;
			} else {
				$fields		= Arr::overwrite($fields, $post->as_array());
				$errors 	= Arr::overwrite($errors, $post->errors('validation'));
				$buffers	= $errors;
				foreach ($errors as $row_key => $row_val) {
					if ($row_val != '') {
						$buffers[$row_key]	= Lib::config('admin.error_field_open').ucfirst($row_val).Lib::config('admin.error_field_close');
					} else {
						$buffers[$row_key]	= $row_val;
					}
				}
				$errors		= $buffers;
			}
		} else {
			$fields	= array('division_id'		=> $this->career->division_id,
							'subject'			=> $this->career->subject,
							'name'				=> $this->career->name,
							'sent_to'			=> $this->career->sent_to,
							'ref_no'			=> $this->career->ref_no,
							'start_date'		=> $this->career->start_date,
							'end_date'			=> $this->career->end_date,
							'report_to'			=> $this->career->report_to,
							'job_purpose'		=> $this->career->job_purpose,
							'responsibilities'	=> $this->career->responsibilities,
							'requirements'		=> $this->career->requirements,
							'location'			=> $this->career->location,
							'company'			=> $this->career->company,
							'ext_link1'			=> $this->career->ext_link1,
							'ext_link2'			=> $this->career->ext_link2,
							'status'			=> $this->career->status);
		}

		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'	  => $fields,
									'statuses'	  => $this->statuses,
									'career'	  => $this->career,
									'class_name' => $this->_class_name,
									'divisions'		=> $this->divisions,
									'settings'		=> $this->settings,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;       
    }
	
	/*** Function Access ***/

	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				$this->career->id	= $row;
				if (!$this->career->load())
					continue;
				$this->career->status	= $this->request->post('select_action');
				$this->career->update();
			}
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);
		} else {
			$this->redirect(ADMIN.$this->_class_name);
		}
	}
	
   public function _valid_search_key (Validation $array, $field) {
		if (!isset($this->_search_keys)) {
			$array->error($field, 'invalid_search_key');
			return;
		}

		$keys			= array_keys($this->_search_keys);

		if (!in_array($array[$field], $keys))
			$array->error($field, 'invalid_search_key');
	}
} // End Event
