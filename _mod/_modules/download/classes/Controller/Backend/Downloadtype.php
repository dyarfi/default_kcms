<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_DownloadType extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	protected $downloadtype;
	protected $file;
	protected $statuses;
	protected $_current_menu;
	
	public function before () {
		parent::before();
		$this->session = Session::instance();
		$this->_search_keys = array('name' => 'Name', 'subject' => 'Subject', 'description' => 'Description', 'status' => 'Status');

		$this->now = time();

		$this->_module_name = 'download';
		$this->_class_name	= strtolower(str_replace('Controller_Backend_', '', get_class($this)));
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_prefilter_keys = array('type_id');
		
		$this->_prefs = (Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->_upload_path = (Lib::config($this->_module_name.'.'.$this->_class_name.'_upload_path') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name.'_upload_path') : array();
		
		$this->_upload_url = (Lib::config($this->_module_name.'.'.$this->_class_name.'_upload_url') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name.'_upload_url') : array();

		$this->downloadtype	= new Model_DownloadType;
		$this->file			= new Model_DownloadTypeFile;
		
		$this->_current_menu = $this->session->get('current_menu');
		
		$this->statuses = array('publish', 'unpublish');   
		
		// Config Visible to member
		$this->_visible			= Lib::config('download.visible');
		
		unset($category, $categories, $buffers);
	}
	public function action_index () {	
		// Default conditions
		$where_cond	= array('status !='	=> 'deleted');
		
		/** Find & Multiple change status **/
		if ($_POST) {			
			$post	= new Validation($_POST);
			if (isset($post['field']) || isset($post['keyword'])) {
				$post->rule('keyword', 'regex', array(':value', '/^[a-z_.\-]++$/iD'));
				
				if ($post->check()) {
					if (!in_array($post['field'], $this->_prefilter_keys))
						$where_cond[$post['field'] . ' LIKE']	= $post['keyword'].'%';
					else
						$where_cond[$post['field'] . ' IN']	= call_user_func(array($this, '_'.$post['field']), $post['keyword']);
					
					$filters	= array('f'	=> $post['field'], 'q'	=> $post['keyword']);					
					$this->session->set($this->_class_name.'_filter', serialize($filters));				
				} else if (isset($post['find'])) {
					$this->session->delete($this->_class_name.'_filter');
				}
			}	
			if ($this->session->get($this->_class_name.'_filter') !== FALSE) {
				$filters	= unserialize($this->session->get($this->_class_name.'_filter'));
				if (in_array($filters['f'], array_keys($this->_search_keys)) && $filters['q'] != '') {
					if (!in_array($filters['f'], $this->_prefilter_keys))
						$where_cond[$filters['f'] . ' LIKE']	= $filters['q'].'%';
					else
						$where_cond[$filters['f'] . ' IN']	= call_user_func(array($this, '_'.$filters['f']), $filters['q']);
					}	
				}
				if(isset($post['per_page'])) {	
					$this->session->set($this->_class_name.'_per_page', (($post['per_page'] != 0) ? $post['per_page'] : Lib::config('admin.item_per_page')));			
				}
			}		
			
			/** Table sorting **/
			
			//if ($type_id != '')			
			//$params		= $this->uri->segment_array(4, TRUE);
			//else			
			//$params		= $this->uri->segment_array(3, TRUE);		
			
			$params		= Request::$current->param();		
			$sorts		= array('asc', 'desc');		
			$sort		= isset($params['id2']) ? $this->id2 : 'subject';
			$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
			$order_by	= array($sort 	=> $order);
			$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
			$per_page	= Lib::config('admin.item_per_page');	
			//$per_page	= 10;
			$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
			$base_url	= ADMIN.$this->_class_name;
			$offset		= ($page_index == 0) ? '' : $page_index * $per_page;
			
			$table_headers	= array('subject'		=> 'Subject',
									'description'	=> 'Description',
									'visible'		=> 'Visible to (Members)',
									'status'		=> 'Status',
									'added'			=> 'Added',
									'modified'		=> 'Modified');
			
			if (isset($params['sort']) && isset($params['order'])) {
				$headers	= array_keys($table_headers);
				$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
				$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];
				$order_by	= array($sort=> $order);
				
				if ($type_id != '')
					$base_url	= ADMIN.$this->_class_name.'/index/'.$type_id.'/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
					else
						$base_url	= ADMIN.$this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
			}		
			
			/** Execute list query **/
			$field		= isset($filters['f']) ? $filters['f'] : '';
			$keyword	= isset($filters['q']) ? $filters['q'] : '';
			$where_cond	= isset($where_cond) ? $where_cond : '';
			$total_rows	= count($this->downloadtype->find($where_cond));
			$total_record 	= $total_rows;
			$listings	= $this->downloadtype->find($where_cond, $order_by, $per_page, $offset);
			$pagination = Pagination::factory(array(
					'total_items' 		=> $total_rows,
					'items_per_page' 	=> $per_page,
				)
			);	
			
			/** Views **/
			$content_vars = array('listings'	=> $listings,
								'table_headers'	=> $table_headers,
								'statuses'		=> $this->statuses,
								'search_keys'	=> $this->_search_keys,
								'visible'		=> $this->_visible,
								'total_record'	=> $total_record,			
								'field'			=> $field,
								'keyword'		=> $keyword,
								'order'			=> $order,
								'sort'			=> $sort,
								'page_url'		=> $page_url,
								'page_index'	=> $offset,
								'per_page'		=> $per_page,
								'params'		=> $params,
								'pagination'	=> $pagination,
								'module_menu'	=> $this->_module_menu,
								'module_name'	=> $this->_module_name,
								'class_name'	=> $this->_class_name);
			$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');
			
			foreach ($content_vars as $var => $val) {
				$content->$var	= $val;		
			}
			
			$this->template->content		= $content; 	
			
		}
	public function action_add () {	
			$fields	= array('subject'		=> '',
							'name'			=> '',
							'description'	=> '',
							'visible'		=> '',
							'status'		=> '');
			
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
					$post	= Validation::factory(array_merge($_POST,$_FILES));
				else
					$post	= Validation::factory($_POST);
				
				
				//$post->rule('type_id', 'not_empty');
				$post->rule('subject', 'not_empty');
                $post->rule('subject', 'min_length', array(':value', 4));
                $post->rule('name', 'not_empty');
                $post->rule('name', 'min_length', array(':value', 4));
                $post->rule('description', 'not_empty');
                $post->rule('description', 'min_length', array(':value', 4));
                $post->rule('status', 'not_empty');
				
				$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
				
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {

						if (isset($row_params['optional']) != 1) {
							$post->rule($row_name, 'Upload::not_empty');
						}

						if (isset($row_params['file_type'])) {
							$post->rule($row_name, 'Upload::type', array(':value', array($row_params['file_type'])));
						} 
						
						if (isset($row_params['max_file_size'])) {
							$post->rule($row_name, 'Upload::size', array(':value', $row_params['max_file_size']));
						}  
						
					}
				}
				
				if ($post->check()) {
					$fields	= $post->as_array();
					$params	= array(
									'subject'		=> $fields['subject'],
									'name'			=> $fields['name'],
									'description'	=> $fields['description'],
									'visible'		=> $fields['visible'],
									'user_id'		=> (isset($this->acl->user->id)) ? $this->acl->user->id : 0,
									'status'		=> $fields['status']);
					
					$id		= $this->downloadtype->add($params);
					
					if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
						foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
							
						//if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
							//continue;

							if (!$post[$row_name]['name'])
								continue;
								
							$file_hash	= md5(time() + rand(100, 999));
							$file_data	= pathinfo($post[$row_name]['name']);

							$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);

							$file_data	= pathinfo($file_name);
							$file_mime	= $post[$row_name]['type'];

							if ($file_name != '') {
								$params = array('type_id'	 => $id,
												'field_name' => $row_name,
												'file_name'	 => $file_data['basename'],
												'file_type'	 => $file_mime,
												'caption'	 => isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');

								$this->file->add($params);
							}
						}
					}
						
				
					$this->session->set('function_add', 'success');

					if (isset($post['add_another'])) {
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
		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'statuses'	=> $this->statuses,
									'visible'	=> $this->_visible,
									'class_name' 	=> $this->_class_name,
									'module_name' 	=> $this->_module_name,
									'module_menu'	=> $this->_module_menu);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');
		
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;	
		}
		
		$this->template->content		= $content;
	}
	public function action_view () {
		$id = $this->id1;

		$this->downloadtype->id	= $id;		
		if (!$this->downloadtype->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;		
		}
		
		$where_cond			= array('type_id'	=> $this->downloadtype->id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();

		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}

		$files				= $buffers;

		unset($buffers);
		
		/** Generate Thumbnails **/
		
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		
		/** Views **/
		
		$content_vars		= array(
									'files'	  		=> $files,
									'download'		=> $this->downloadtype,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'module_menu'	=> $this->_module_menu,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name,
									'visible'		=> $this->_visible,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime')									);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_view');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;	
		}
		
		$this->template->content		= $content;     
	}	
	public function action_edit () {
		$id = $this->id1;

		$this->downloadtype->id	= $id;
		if (!$this->downloadtype->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array(
						'subject'		=> '',
						'name'			=> '',
						'description'	=> '',
						'visible'		=> '',
						'status'		=> '');
		
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
			$post->rule('name', 'min_length', array(':value', 4));
			$post->rule('description', 'not_empty');
            $post->rule('description', 'min_length', array(':value', 4));
            $post->rule('status', 'not_empty');
			
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					// if (isset($row_params['file_type']))
						// $post->add_rules(substr($post[$row_name]['type'], strpos($post[$row_name]['type'], '/') + 1), 'upload::type['.$row_params['file_type'].']');

					// if (isset($row_params['max_file_size']))
						// $post->add_rules(round($post[$row_name]['size'] / 1024, 2).'KB', 'upload::size['.$row_params['max_file_size'].']');
						
					if (!File::exts_by_mime($post[$row_name]['type']))
						continue;
				}
			}
			
			if ($post->check()) {
				$fields	= $post->as_array();
				$params	= array('subject'		=> $fields['subject'],
								'name'			=> $fields['name'],
								'description'	=> $fields['description'],
								'visible'		=> $fields['visible'],
								'status'		=> $fields['status']);
				
				foreach ($params as $var => $val) {
					$this->downloadtype->$var	= $val;
				}
				
				$this->downloadtype->update();
				
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('type_id'	=> $this->downloadtype->id);
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
						
						if($row_params['caption'] == true && empty($post[$row_name]['size']) && !empty($files[$row_name]->id)) {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$this->file->caption = ($this->file->caption == $fields[$row_name.'_caption']) ? $this->file->caption : $fields[$row_name.'_caption']; 								
							$this->file->update();
						}
						
						//! Upload::valid($image) OR
						//! Upload::not_empty($image) OR
						//! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
						
						/*
						if (!isset($_FILES[$row_name]) || (isset($_FILES[$row_name]) && !Upload::type($_FILES[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($_FILES[$row_name])))
							continue;
						*/
						
						if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
							continue;
						
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						
						if (!isset($files[$row_name])) {
							$params			= array('type_id'		=> $this->downloadtype->id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							
							$this->file->add($params);
						} else {
						
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();

							$params			= array('type_id'		=> $this->downloadtype->id,
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

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->downloadtype->id); return;
							
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
			$fields	= array('subject'			=> $this->downloadtype->subject,
							'name'				=> $this->downloadtype->name,
							'description'		=> $this->downloadtype->description,
							'visible'			=> $this->downloadtype->visible,
							'status'			=> $this->downloadtype->status);
			
			$where_cond			= array('type_id'	=> $this->downloadtype->id);
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
		//$where_cond			= array('status !='		=> 'deleted');
		//$order_by			= array('category_id'	=> 'ASC');
		//$orders				= $this->downloadtypetype->find($where_cond, $order_by);
		
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'downloadtype'	=> $this->downloadtype,
									'files'			=> $files,
									'statuses'		=> $this->statuses,
									'visible'	=> $this->_visible,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'module_menu'	=> $this->_module_menu,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name,
									'current_menu'	=> $this->_current_menu,
									'statuses'		=> $this->statuses,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime'));
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;		
		}
		
		$this->template->content		= $content;	
	}
	public function action_delete () {
		$id = $this->id1;
		$this->downloadtype->id	= $id;
		if (!$this->downloadtype->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$this->downloadtype->status	= 'deleted';
		if($this->downloadtype->update())
			echo 1; //echo $this->id2;
		else
			echo 0;
		exit;
		//		$this->session->set_flash('function_delete', 'success');////		
		$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';
		//		$this->redirect($redirect_url);	
	}	
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				$this->downloadtype->id	= $row;
				if (!$this->downloadtype->load())
					continue;
				$this->downloadtype->status	= $this->request->post('select_action');
				$this->downloadtype->update();
			}
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);
		} else {
			$this->redirect(ADMIN.$this->_class_name);	
		}
	}	
	public function action_download() {
		$files		= $this->id1;
		
		$where_cond	= array('file_name'	=> $files);

		$files		= $this->file->find($where_cond);

		foreach ($files as $row) {
			Lib::_download_file_force('',$this->_upload_url.$row->file_name);
		}
	}	
	/** CALLBACKS **/	
	public function _valid_media_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;
		$where_cond		= array('id'	=> $array[$field]);		
		$parent_exists	= ($this->category->find_count($where_cond) != 0);
		if (!$parent_exists)
			$array->add_error($field, 'invalid_media_id');	
	}	
	public function _unique_name (Validation $array, $field) {		
		if (isset($this->downloadtype->name) && $this->downloadtype->name == $array[$field])
			return;	
		$where_cond		= array('name'	=> $array[$field]);		
		$name_exists	= ($this->downloadtype->find_count($where_cond) != 0);
		if ($name_exists)
			$array->error($field, 'name_exists');	
	}
	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->add_error($field, 'invalid_status');		
	}	
	/** PRE FILTER **/
	public function _reverse_date ($value = '') {		
		if (strpos($value, '/') != 0)
			return (implode('-', array_reverse(explode('/', $value))));
		else
			return (implode('/', array_reverse(explode('-', $value))));	
	}	
	public function _media_id ($value = '') {
		$where_cond	= array('name LIKE'	=> '%'.$value.'%','status !='	=> 'deleted');
		$buffers	= $this->category->find($where_cond);
		$ids		= array();		
		foreach ($buffers as $row) {
			$ids[]	= $row->id;
			}
		return $ids;	
	}
}