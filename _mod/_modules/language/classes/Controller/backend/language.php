<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Language extends Controller_Themes_DefaultAdmin {
    
    protected $id1; 
	protected $id2; 
	protected $id3;
    protected $id4;
	
	public function before () {
		// Get parent before method
        parent::before();

		$this->user			= new Model_User;
		
        $this->id1 = Request::$current->param('id1');
        $this->id2 = Request::$current->param('id2');
        $this->id3 = Request::$current->param('id3');
		$this->id4 = Request::$current->param('id4');
		$this->id5 = Request::$current->param('id5');
		
		$this->_class_name		= Request::$current->controller();
		$this->_class_name		= $this->controller;	
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->language		    = new Model_Language;

		$this->statuses			= array(1=>'publish',
										0=>'unpublish');
		
		$this->_search_keys		= array('name' => 'Name',
										'prefix' => 'Prefix');		
    }
	
	public function action_index() {
		
		$where_cond	= array('status'	=> 1);

		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if (isset($post['field']) || isset($post['keyword'])) {
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
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN.$this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('name'				=> 'Name',
								'prefix'			=> 'Prefix',
								'status'			=> 'Status',
								'default'			=> 'Default',
								'is_system'			=> 'System',
								'added'				=> 'Added',
								'modified'			=> 'Modified');

		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);

			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];

			$order_by	= array($sort	=> $order);

			$base_url	= ADMIN.$this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
		}

		/** Execute list query **/

		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';

		$where_cond	= isset($where_cond) ? $where_cond : '';

		$total_rows	= $this->language->find_count($where_cond);
		$total_record = $total_rows;
		$listings	= $this->language->find($where_cond, $order_by, $per_page, $offset);

		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);

			Request::$current->redirect($base_url.$page_index);
			return;
		}

		//$this->session->get($this->_class_name.'_index', $base_url.$page_index);

		/** Initialize pagination **/

		$pagination = Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 )
		);

        /** Generate Thumbnails **/

		//$this->_auto_image_manipulation();

		/** Views **/

		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'total_record'	=> $total_record,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $offset,
									'params'		=> $params,
									'pagination'	=> $pagination);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	
		
	}
    
  public function action_view() {
	
		$id			= $this->id1;
		$lang_id	= $id;
		$this->language->id	= $id;

		if (!$this->language->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		/** Views **/

		// Language list data with PUBLISH status
		$language_id = $this->language->find(array('status'=>1));
		$buffers_id = array();
		foreach($language_id as $langid){
			$buffers_id[$langid->id] = $langid->id;
		}
		$language_id	= $buffers_id;
		
		// Language list file data
		$language_file = $this->language->find(array('file_name !=' => ''));
		foreach ($language_file as $lang_file) {
			$buffer_files[$lang_file->id] = $lang_file->file_name;
		}
		$language_file  = $buffer_files;
		
		unset($buffers, $buffers_id, $buffer_files);
		
		/** Generate Thumbnails **/

		$content_vars		= array('listings'		=> $this->language,
									'lang_id'		=> $lang_id,
									'language_file'	=> $language_file,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									/*'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime')*/);

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;     
    }
	
	public function action_edit () {
		$id					= $this->id1;
		$this->language->id	= $id;

		if (!$this->language->load()) {
			//$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');			
			//print_r(HTTP_Exception::$error_view); exit();
			$this->redirect(ADMIN.$this->_class_name.'/errors/404');
			return;
		}

		$fields	= array('parameter'			=> '',
						'alias'				=> '',
						'value'				=> '',
						'editable'			=> '',
						'status'			=> '');

		$errors	= $fields;

		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);

			if ($post->check()) {
				$fields	= $post->as_array();
                
				$params	= array('prefix'			=> Inflector::underscore($fields['prefix']),
								'alias'				=> $fields['alias'],
								'value'				=> $fields['value'],
								'editable'			=> $fields['editable'],
								'status'			=> $fields['status']);

				foreach ($params as $var => $val) {
					$this->setting->$var	= $val;
				}

				$this->setting->update();

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->setting->settingId);
				return;
			} else {
				$fields		= Arr::overwrite($fields, $post->as_array());
				$errors 	= Arr::overwrite($errors, $post->errors());
				$buffers	= $errors;

				foreach ($errors as $row_key => $row_val) {
					if ($row_val != '')
						$buffers[$row_key]	= Lib::config('site.error_field_open').Kohana::lang('validation.'.$errors[$row_key]).Lib::config('site.error_field_close');
					else
						$buffers[$row_key]	= $row_val;
				}

				$errors		= $buffers;
			}
		} else {
		  
			$fields	= array('prefix'		=> $this->language->prefix,
							'name'			=> $this->language->name,
							'default'		=> $this->language->default,
							'file_name'		=> $this->language->file_name,
							'status'		=> $this->language->status,
							'is_system'		=> $this->language->is_system,
							'added'			=> $this->language->added,
							'modified'		=> $this->language->modified);

		}

		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'language'		=> $this->language,
									'statuses'		=> $this->statuses,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'));

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}
	
	public function action_add () {

		$fields	= array('name'		=> '',
						'prefix'	=> '',
						'default'	=> '',
						'status'	=> '');

		$errors	= $fields;
		
		if ($_POST) {
			$post = Validation::factory($_POST)			
					->rule('name', 'not_empty')
					->rule('name', 'min_length', array(':value', 4))
					->rule('prefix', 'not_empty')
					->rule('prefix', 'min_length', array(':value', 1))
					->rule('status', 'not_empty');
			
			if ($post->check()) {
				$fields	= $post->as_array();

				$params	= array('name'		=> $fields['prefix'],
								'prefix'	=> Inflector::underscore($fields['prefix']),
								'status'	=> $fields['status']);

				$id		= $this->language->add($params);

				if (isset($fields['add_another'])) {
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
		
		$content_vars		= array(
								'errors'	=> $errors,
								'fields'	=> $fields,
								'statuses'	=> $this->statuses,
								'class_name' 	=> $this->_class_name,
								'module_menu'	=> $this->_module_menu);

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}
	
	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				$this->language->id	= $row;
				if (!$this->language->load())
					continue;
				$this->language->status	= $this->request->post('select_action');
				$this->language->update();
			}
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);
		} else {	
			$this->redirect(ADMIN.$this->_class_name);			
		}
	}	
	    
} // End Language
