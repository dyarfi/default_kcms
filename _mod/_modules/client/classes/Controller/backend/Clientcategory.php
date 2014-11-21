<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Backend_ClientCategory extends Controller_Backend_BaseAdmin {
    
    protected $clientCategory;
    
	protected $_upload_path;
	protected $_upload_url;

	public function before () {
		// Get parent before method
        parent::before();
		
        $this->clientCategory	= new Model_ClientCategory;
		$this->file				= new Model_ClientCategoryFile;
        
		$this->_module_name		= 'client';
		$this->_class_name		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->statuses			= array('publish','unpublish');
		
		$this->_search_keys		= array('name'			=> 'Name',
										'description'	=> 'Description',
										'status'		=> 'Status');
		$this->_prefilter_keys	= array('category_id');
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();

    }
    
    public function action_index() {       
        $where_cond	= array('id !=' => 0);
		//$this->clientCategory->find($where_cond);
		//exit();
		//$order_by	= array('added' => 'DESC');

		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if ($this->id1 == 'select_action' && isset($_POST['check'])) {
				$rows	= $_POST['check'];

				foreach ($rows as $row) {
					$this->clientCategory->id	= $row;

					if (!$this->clientCategory->load())
						continue;

					$this->clientCategory->status	= $_POST['select_action'];
					$this->clientCategory->update();
				}

				$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

				$this->redirect($redirect_url);
				return;
			}

			if (isset($_POST['field']) || isset($_POST['keyword'])) {
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
								'description'		=> 'Description',
								'status'			=> 'Status',
								'added'				=> 'Added',
								'modified'			=> 'Modified'
								);

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

		$total_rows		= $this->clientCategory->find_count($where_cond);
		$total_record 	= $total_rows;
		
		$listings	= $this->clientCategory->find($where_cond, $order_by, $per_page, $offset);
		
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
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									//'language'	=> $this->language,
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

		//$content			= array();
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;
    }
	
	public function action_add () {
		
		$fields	= array('name'				=> '',
						'subject'			=> '',
						'description'		=> '',
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

			$post->rule('subject', 'not_empty');
			$post->rule('subject', 'min_length', array(':value', 4));
			$post->rule('name', 'not_empty');					
			$post->rule('name', array($this, '_unique_name'), array(':validation', ':field', 'name'));

			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional']) {
						//$post->add_rules($row_name, 'upload::required');
						//$post->add_rules($row_name, 'upload::valid');
					//}

					//if (isset($row_params['file_type']))
						//$post->add_rules($row_name, 'upload::type['.$row_params['file_type'].']');

					//if (isset($row_params['max_file_size']))
						//$post->add_rules($row_name, 'upload::size['.$row_params['max_file_size'].']');
				}
			}

			//$post->add_callbacks('name', array($this, '_unique_name'));

			if ($post->check()) {
				$fields	= $post->as_array();
	
				//$fields['name']	= $this->_remove_tag($fields['name']);

				$params	= array('subject'			=> strip_tags($fields['subject']),
								'name'				=> strip_tags($fields['name']),
								'description'		=> $fields['description'],
								'status'			=> $fields['status']);

				$id		= $this->clientCategory->add($params);

				if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						if (!upload::required($_FILES[$row_name]) || !upload::valid($_FILES[$row_name]))
							continue;

						$file_hash	= md5(mktime() + rand(100, 999));
						$file_data	= pathinfo($_FILES[$row_name]['name']);
						$file_name	= Lib::_upload_to($row_name, $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0777);
						$file_data	= pathinfo($file_name);
						$file_mime	= $_FILES[$row_name]['type'];
						
						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params			= array('category_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');

							$this->file->add($params);
						}
					}
				}

				if (isset($_POST['add_another'])) {
					$this->redirect(ADMIN . $this->_class_name.'/add');
					return;
				}

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$id);
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
		}

		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name);

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}

	public function action_view ( ) {
		$this->clientCategory->id	= $this->id1;

		if (!$this->clientCategory->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}

		/** Views **/
		
		$where_cond			= array('category_id'	=> $this->clientCategory->id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();

		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}

		$files				= $buffers;

		unset($buffers);

		/** Generate Thumbnails **/
		
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		
		$content_vars		= array('category' => $this->clientCategory,
									//'division'		=> $division,
									'files'			=> $files,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									//'order'			=> $order,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,	
									'class_name'	=> $this->_class_name,	
									//'headline'		=> $this->_headline,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime')
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');
		
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_edit ( ) {
		$category_id				= $this->id1;
		$this->clientCategory->id	= $category_id;
		
		if (!$this->clientCategory->load()) {
			$this->redirect(ADMIN . 'error/invalid_request');
			return;
		}

		$fields	= array(
						'name'				=> '',
						'subject'			=> '',
						'description'		=> '',
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

			$post->rule('name', 'not_empty');
			$post->rule('name', 'min_length', array(':value', 4));

			if (isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional'])
						//$post->rule($row_name, 'upload::valid');

					//if (isset($row_params['file_type']))
						//$post->rule($row_name, 'upload::type['.$row_params['file_type'].']');

					//if (isset($row_params['max_file_size']))
						//$post->rule($row_name, 'upload::size['.$row_params['max_file_size'].']');
					
					//if (!File::exts_by_mime($post[$row_name]['type']))
						//continue;
				}
			}

//			$post->add_callbacks('name', array($this, '_unique_name'));

			if ($post->check()) {
				$fields	= $post->as_array();
	
				//$fields['name']	= $this->_remove_tag($fields['name']);

				$params	= array(
								'subject'			=> strip_tags($fields['subject']),
								'name'				=> $fields['name'],
								'description'		=> $fields['description'],
								'status'			=> $fields['status']);

				foreach ($params as $var => $val) {
					$this->clientCategory->$var	= $val;
				}

				$this->clientCategory->update();

				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('category_id'	=> $this->clientCategory->id);
					$files				= $this->file->find($where_cond);
					$buffers			= array();

					foreach ($files as $row) {
						$buffers[$row->field_name]	= $row;
					}

					$files				= $buffers;

					unset($buffers);

					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						if (isset($fields['delete_'.$row_name]) && $fields['delete_'.$row_name] == 1 && isset($files[$row_name])) {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$this->file->delete();
						}
						
						if($row_params['caption'] == true && empty($_FILES[$row_name]['size']) && !empty($files[$row_name]->id)) {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$this->file->caption = ($this->file->caption == $fields[$row_name.'_caption']) ? $this->file->caption : $fields[$row_name.'_caption']; 								
							$this->file->update();
						}
						
						if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
							continue;

						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0777);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						
						if (!isset($files[$row_name])) {
							$params			= array('category_id'	=> $category_id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							
							$this->file->add($params);
						} else {
						
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();

							$params			= array('category_id'	=> $category_id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							
							foreach ($params as $var => $val) {
								$this->file->$var	= $val;
							}

							$this->file->update();
						}

					}
				}

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$this->clientCategory->id);
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
			$fields	= array(
							'name'				=> $this->clientCategory->name,
							'subject'			=> $this->clientCategory->subject,
							'description'		=> $this->clientCategory->description,
							'status'			=> $this->clientCategory->status);

			$where_cond			= array('category_id'	=> $this->clientCategory->id);
			$files				= $this->file->find($where_cond);
			$buffers			= array();

			foreach ($files as $row) {
				$buffers[$row->field_name]	= $row;
			}

			$files				= $buffers;

			unset($buffers);

			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					$fields[$row_name]	= '';

					if (isset($row_params['caption']) && $row_params['caption'])
						$fields[$row_name.'_caption']	= (isset($files[$row_name])) ? $files[$row_name]->caption : '';
				}
			}
		}

		/** Views **/

		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'category'	=> $this->clientCategory,
									'statuses'	=> $this->statuses,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'category_id'	=> $category_id,
									'files'		=> $files,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');
		
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}

	public function action_delete ($id = '') {
		$this->clientCategory->id	= $id;

		if (!$this->clientCategory->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}

		$this->clientCategory->status	= 'deleted';
		if($this->clientCategory->update()) {
			echo 1;
		}	
		else {
			echo 0;
		}
		
		exit;

//		$this->session->set_flash('function_delete', 'success');
//
//		$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : 'admin-cp/'.$this->_class_name.'/index';
//
//		$this->redirect($redirect_url);
	}

	/** CALLBACKS **/

	public function action_download() {
		$files		= $this->id1;
		$where_cond	= array('file_name'	=> $files);
		$files		= $this->file->find($where_cond);
		foreach ($files as $row) {
			Lib::_download(Lib::config($this->_class_name.'.upload_url').$row->file_name);
		}
	}
	
	// Action for update item status
	public function action_change() {
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->clientCategory->id	= $row;

				if (!$this->clientCategory->load())
					continue;

				$this->clientCategory->status	= $this->request->post('select_action');
				$this->clientCategory->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN.$this->_class_name);
			
		}
		
	}
	
/** CALLBACKS **/
	public function _file_type (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		$valid = Upload::type($array[$field], explode(',',array('gif','jpg','png')));
		if ($valid)
			return $array->error($field, 'file_type');
	}
	
	public function _unique_name (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		$name = Model_PageCategory::instance()->find_count(array('name' => $array[$field]));
		if ($name)
			return $array->error($field, 'unique_name');
	}
	
	public function _valid_parent_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;
		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->category->find_count($where_cond) != 0);
		if (!$parent_exists)
			$array->error($field, 'invalid_parent_id');
	}
	
	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->error($field, 'invalid_status');
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
    
} // End ClientCategory
