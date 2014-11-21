<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Url extends Controller_Backend_BaseAdmin {
    
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
	protected $_upload_path;
	protected $_upload_url;
	
	protected $url;
    protected $_users;
	protected $_uid;
    
	public function before () {
		// Get parent before method
        parent::before();

		$this->url      = new Model_Url;
		$this->urlLog   = new Model_UrlLog;
		$this->user		= new Model_User;	
        
		$this->_class_name		= Request::$current->controller();
		$this->_class_name		= $this->controller;	
		$this->_module_menu		= $this->acl->module_menu;
				
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();

		$this->_search_keys		= array('url'		=> 'Url',
                                        'keywords'	=> 'Keywords',
                                        'title'     => 'Title',
										'ip'		=> 'Ip Address');
	
		$users					= $this->user->find();
		
		$buffers				= array();
		foreach ($users as $user){
			$buffers[$user->id] = $user;
		}
		$this->_users			= $buffers;		
		unset($buffers);
		
		//-- User id from user login session 'user_id'
		$this->_uid				= $this->session->get('user_id');
		
		//-- Default url statuses
		$this->statuses			= array('inactive','active');
	
	}
    
    public function action_main() { $this->template->content = 'Default News'; }
    
    public function action_index() {       
		//$where_cond	= array('status' => 'publish');
		//$this->url->find($where_cond);
		//exit();
		//$order_by	= array('added' => 'DESC');

		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if (isset($post['field']) || isset($post['keyword'])) {
				$post->rule('field', array($this, '_valid_search_key'), array(':validation', 'field'));
				$post->rule('keyword', 'regex', array(':value', '/^[a-z0-9_.\s\-]++$/iD'));

				if ($post->check()) {
					$where_cond[$post['field'] . ' LIKE']	= $post['keyword'] . '%';

					$filters	= array('f'	=> $post['field'],
										'q'	=> $post['keyword']);

					$this->session->set($this->_class_name.'_filter', serialize($filters));
				} else if (isset($post['find'])) {
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

		$sort		= isset($params['id2']) ? $this->id2 : 'id';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[1];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN . $this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('url'			=> 'Url',
								'keywords'		=> 'Keywords',
								'title'			=> 'Title',			
								'clicks'		=> 'Clicks',
								'status'		=> 'Status',
								'ip'			=> 'Ip Address',	
								'timestamp'		=> 'Time Stamp');

		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);

			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];

			$order_by	= array($sort	=> $order);

			$base_url	= ADMIN . $this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
		}

		/** Execute list query **/

		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';

		$where_cond	= isset($where_cond) ? $where_cond : '';

		//$total_rows	= $this->url->find_count($where_cond);
		
		$total_rows		= $this->url->find_count($where_cond);
		$total_record 	= $total_rows;
		
		//$listings	= $this->url->find($where_cond, array_merge($order_by, array('subject'=>'asc')), $per_page, $offset);
		
		$listings	= $this->url->find($where_cond, $order_by, $per_page, $offset);
		
		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);
			//$this->redirect($base_url.$page_index);
			//return;
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
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'search_keys'	=> $this->_search_keys,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $offset,
									//'params'		=> $params,
									'total_record'	=> $total_record,
									'pagination'	=> $pagination);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
    }
    
    public function action_add() {

		$fields	= array(
						'keywords'  => '',
						'url'		=> '',
						'title'		=> '');

		$errors	= $fields;

		if ($_POST) {
			if ($_FILES) 
				$post	= Validation::factory(array_merge($_POST,$_FILES));
			else
				$post	= Validation::factory($_POST);
			
                $post->rule('keywords', 'not_empty');
				$post->rule('keywords', 'regex', array(':value', '/^[0-9a-z_.\@\&\-]++$/iD'));
				$post->rule('keywords', array($this, '_unique_keyword'), array(':validation', 'keywords'));
				$post->rule('keywords', 'min_length', array(':value', 4));
				$post->rule('keywords', 'max_length', array(':value', 10));
                
                $post->rule('url', 'not_empty');
				$post->rule('url', 'Valid::url');
                
				//$post->rule('title', 'not_empty');
                $post->rule('title', 'min_length', array(':value', 4));
            
			if ($post->check()) {
				$fields	= $post->as_array();

				$params	= array(
						'keywords'		=> $fields['keywords'],
						'url'			=> $fields['url'],
						'title'			=> $fields['title'],
						'timestamp'		=> date('Y-m-d h:m:s'),
						'ip'			=> Request::$client_ip,
						'clicks'		=> 0,
						'status'		=> 1
					);

				// Add params for database insert
				$keywords = $this->url->add($params);
						
				$this->session->set('function_add', 'success');

				if (isset($post['add_another'])) {
					$this->redirect(ADMIN . $this->_class_name.'/add');
					return;
				}

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$keywords);
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
		}
		
		/** Views **/

		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'statuses'	=> $this->statuses,
									'class_name' 	=> $this->_class_name,
									'module_menu' 	=> $this->_module_menu);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
    }
    
	public function action_edit () {
		$this->url->id	= $this->id1;

		if (!$this->url->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/error/invalid_request');
			return;
		}

		$fields	= array(
						'keywords'  => '',
						'url'		=> '',
						'title'		=> '');

		$errors	= $fields;

		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);

			$post->rule('keywords', 'not_empty');
			$post->rule('keywords', 'regex', array(':value', '/^[0-9a-z_.\@\&\-]++$/iD'));
			//$post->rule('keywords', 'min_length', array(':value', 4));
			//$post->rule('keywords', 'max_length', array(':value', 10));
			//$post->rule('keywords', array($this, '_unique_keyword'), array(':validation', 'keywords'));

			$post->rule('url', 'not_empty');
			$post->rule('url', 'Valid::url');
                
			
			if ($post->check()) {
				$fields	= $post->as_array();
				$params	= array(
						'keywords'		=> $fields['keywords'],
						'url'			=> $fields['url'],
						'title'			=> $fields['title']
					);

				foreach ($params as $var => $val) {
					$this->url->$var	= $val;
				}

				$this->url->update();

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$this->url->keywords);
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
		} else {
			$fields	= array(
							'keywords'			=> $this->url->keywords,
							'url'				=> $this->url->url,
							'title'				=> $this->url->title);
		}

		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'url'			=> $this->url);

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;
	}
	
    public function action_view() {

		$this->url->id	= $this->id1;
		
		if (!$this->url->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}
		
		/** Views **/
		$content_vars		= array(
									'listings'		=> $this->url,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
    }
    
    public function action_delete() {
	
		$this->url->id	= $this->id1;
		
		if (!$this->url->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}
		
		// Set is_deleted / status to TRUE
		// $this->url->status	= 'deleted';
		
		// if($this->url->update())
			// echo 1;
		// else
			// echo 0;
			
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
		
			// Set is_deleted / status to TRUE
			$this->url->status	= '0';
			if($this->url->update())
				echo 1;
			else
				echo 0;	
				
		} else {
			$this->redirect(ADMIN . $this->request->controller());
			//die();
		}

		exit();
    }
    
	public function _unique_keyword (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		
		$keyword = $this->url->load_by_keywords($array[$field]);
		
		if ($keyword)
			return $array->error($field, 'unique_keyword');
	}
	
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {
		
		//print_r($this->request->post('select_action')); exit();
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->url->id	= $row;

				if (!$this->url->load())
					continue;

				$this->url->status	= $this->request->post('select_action');
				$this->url->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN . $this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN . $this->_class_name);
			
		}
		
	}
	
	public function action_check_name() {
		
		$name_check =  $this->url->load_by_name($_POST['name']);
		
		$result = !empty($name_check) ? 1 : 0;
		
		echo $result;
		
		exit();
	}
	
	public function _valid_search_key (Validation $array, $field) {
		if (!isset($this->_search_keys)) {
			$array->error($field, 'invalid_search_key');
			return;
		}

		$keys = array_keys($this->_search_keys);

		if (!in_array($array[$field], $keys))
			$array->error($field, 'invalid_search_key');
	}
  
	/** CALLBACKS **/  
    
    public function _unique_name (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		
		$name = Model_Url::instance()->find_count(array('name' => $array[$field]));
		
		if ($name)
			return $array->error($field, 'unique_name');
	}

} // End Event
