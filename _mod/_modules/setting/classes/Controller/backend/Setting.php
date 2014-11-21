<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Setting extends Controller_Backend_BaseAdmin {
    
    protected $setting;
   
	public function before () {
		// Get parent before method
        parent::before();
		
        $this->setting			= new Model_Setting;
		
		$this->_class_name		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->statuses			= array('publish','unpublish');
		
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->_search_keys		= array('alias'			=> 'Alias',
										'value'			=> 'Value',
										'paremeter'		=> 'Paremeter',
										'status'		=> 'Status');
										
    }
      
    public function action_index () {

		// Get all ajax and post request
		if ($this->request->is_ajax() && $this->request->post()) {
			// Make sure if this is an ajax request and mode is 1 
			if ($this->request->post('ajax') == true) {
				$maintenance_mode = new Model_Configuration;
				$maintenance_mode->load('maintenance');
				$maintenance_mode->parameter = 'maintenance'; 
				$maintenance_mode->value	 = $this->request->post('mode'); 
				$result = $maintenance_mode->update();
				echo $result;
			}
			exit();
		}
		
		$where_cond	= array('status !='	=> 'deleted');

		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if ($this->id1 == 'select_action' && isset($_POST['check'])) {
				$rows	= $_POST['check'];

				foreach ($rows as $row) {
					$this->setting->id	= $row;

					if (!$this->setting->load())
						continue;

					$this->setting->status	= $_POST['select_action'];
					$this->setting->update();
				}

				$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

				$this->redirect($redirect_url);
				return;
			}

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

			if ($this->session->get($this->_class_name.'_filter') !== FALSE) {
				$filters	= unserialize($this->session->get($this->_class_name.'_filter'));

				if (in_array($filters['f'], array_keys($this->_search_keys)) && $filters['q'] != '') {
					if (!in_array($filters['f'], $this->_search_keys))
						$where_cond[$filters['f'] . ' LIKE']	= $filters['q'].'%';
					else
						$where_cond[$filters['f'] . ' IN']	= call_user_func(array($this, '_'.$filters['f']), $filters['q']);
				}
			}
		}

		/** Table sorting **/

		$params		= Request::$current->param();
		$sorts		= array('asc', 'desc');

		$sort		= isset($params['id2']) ? $this->id2 : 'parameter';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($params['page']) ? $params['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		$page_url	= '';
		$base_url	= ADMIN.$this->_class_name.'/index/page/';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('alias'			=> 'Alias',
								'value'			=> 'Value',
								'parameter'		=> 'Parameter',
								'status'		=> 'Status',
								'modified'		=> 'Modified');

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

		$total_rows		= $this->setting->find_count($where_cond);
		$total_record 	= $total_rows;
		
		$listings	= $this->setting->find($where_cond, $order_by, $per_page, $offset);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);
		
		$configuration = Model_Configuration::instance()->load('maintenance');
		
		
		/** Generate Thumbnails **/

		//$this->_auto_image_manipulation();

		/** Views **/

		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'configuration' => $configuration,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'total_record'	=> $total_record,
									'pagination'	=> $pagination);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_view () {
		$id					= $this->id1;
		$this->setting->id	= $id;

		if (!$this->setting->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		/** Views **/
		
		$content_vars		= array('setting'			=> $this->setting,
									'id'				=> $id,
									'class_name'		=> $this->_class_name,
									'module_menu'		=> $this->_module_menu,
                                    'readable_mime'		=> Lib::config($this->_class_name.'.readable_mime')
									);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_add () {
		$fields	= array('alias'				=> '',
						'value'				=> '',
						'parameter'			=> '',
						'is_system'			=> '',
						'status'			=> '');

		$errors	= $fields;
		
		if ($_POST) {
			$post = Validation::factory($_POST)			
					->rule('alias', 'not_empty')
					->rule('alias', 'min_length', array(':value', 4))
					->rule('value', 'not_empty')
					->rule('value', 'min_length', array(':value', 1))
					->rule('parameter', 'not_empty')
					->rule('parameter', 'min_length', array(':value', 4))
					->rule('status', 'not_empty');
			
			if ($post->check()) {
				$fields	= $post->as_array();

				$params	= array('alias'		=> $fields['alias'],
								'value'		=> $fields['value'],
								'parameter'	=> $fields['parameter'],
								'is_system'	=> $fields['is_system'],
								'status'	=> $fields['status']);

				$id		= $this->setting->add($params);

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
								'action'	=> $this->action,
								'param'		=> $this->id1,
								'errors'	=> $errors,
								'fields'	=> $fields,
								'statuses'	=> $this->statuses,
								'class_name' 	=> $this->_class_name,
								'module_menu'	=> $this->_module_menu);
								
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_form');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;
	}
	
	public function action_edit () {
		$id					= $this->id1;
		$this->setting->id	= $id;

		if (!$this->setting->load()) {
			//$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');			
			//print_r(HTTP_Exception::$error_view); exit();
			$this->redirect(ADMIN.$this->_class_name.'/errors/404');
			return;
		}

		$fields	= array('parameter'			=> '',
						'alias'				=> '',
						'value'				=> '',
						'is_system'			=> '',
						'status'			=> '');

		 if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
			 foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				 $fields[$row_name]	= '';

				 if (isset($row_params['caption']) && $row_params['caption'])
					 $fields[$row_name.'_caption']	= '';
			 }
		 }

		$errors	= $fields;

		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);

			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['file_type']))
						//$post->add_rules(substr($post[$row_name]['type'], strpos($post[$row_name]['type'], '/') + 1), 'upload::type['.$row_params['file_type'].']');

					//if (isset($row_params['max_file_size']))
						//$post->add_rules(round($post[$row_name]['size'] / 1024, 2).'KB', 'upload::size['.$row_params['max_file_size'].']');
				}
			}

			if ($post->check()) {
				$fields	= $post->as_array();
                
				$params	= array('parameter'			=> $fields['parameter'],
								'alias'				=> $fields['alias'],
								'value'				=> $fields['value'],
								'is_system'			=> $fields['is_system'],
								'status'			=> $fields['status']);

				foreach ($params as $var => $val) {
					$this->setting->$var	= $val;
				}

				$this->setting->update();

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->setting->id);
				return;
			} else {
				$fields		= arr::overwrite($fields, $post->as_array());
				$errors 	= arr::overwrite($errors, $post->errors());
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
		  
			$fields	= array('parameter'		=> $this->setting->parameter,
							'alias'			=> $this->setting->alias,
							'value'			=> $this->setting->value,
							'is_system'		=> $this->setting->is_system,
							'status'		=> 'publish');
            
			//$where_cond			= array('setting_id'	=> $this->setting->id);
			//$files				= $this->file->find($where_cond);
			//$buffers			= array();

			 //foreach ($files as $row) {
				 //$buffers[$row->field_name]	= $row;
			 //}

			 //$files				= $buffers;

			 //unset($buffers);

			 if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				 foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					 $fields[$row_name]	= '';

					 if (isset($row_params['caption']) && $row_params['caption'])
						 $fields[$row_name.'_caption']	= (isset($files[$row_name])) ? $files[$row_name]->caption : '';
				 }
			 }
		}

		/** Views **/

		$content_vars		= array(
									'action'	=> $this->action,
									'param'		=> $this->id1,
									'errors'		=> $errors,
									'fields'		=> $fields,
									'setting'		=> $this->setting,
									//'files'		=> $files,
									'statuses'		=> $this->statuses,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,	
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'));

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_form');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_delete () {
		$this->setting->id	= $this->id1;

		if (!$this->setting->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		$this->setting->status	= 'deleted';
		if($this->setting->update())
			echo 1;
		else
			echo 0;
		
		exit();
	}
   
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->setting->id	= $row;

				if (!$this->setting->load())
					continue;

				$this->setting->status	= $this->request->post('select_action');
				$this->setting->update();
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
	
} // End Setting Class
