<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_Services extends Controller_Backend_BaseAdmin {
	
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

	protected $services;
	protected $servicess;
	protected $user;
	protected $users;
	protected $statuses;

	private $_parent_services;
	
	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_class_name	= $this->controller;	
		$this->_module_menu	= $this->acl->module_menu;
		
		$this->_search_keys	= array('subject'	=> 'Subject',
									'name'		=> 'Name',
									'status'	=> 'Status');

		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();

		$this->services	= new Model_Services;
		$this->user			= new Model_User;

		$where_cond			= array('status'	=> 'publish');
		$this->servicess		= $this->services->find($where_cond);

		$this->file			= new Model_ServicesFile();
		$this->user			= new Model_User();
		
		$where_cond			= array('status'	=> 'active');
		$this->users		= $this->user->find($where_cond);

		$this->statuses		= array('publish',
									'unpublish');
	}

	function action_index () {
	
		$where_cond	= array('status !='	=> 'deleted');

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
		
		$_files  = $this->file->find();
		$buffers = array();
		foreach ($_files as $_file){
			$buffers[$_file->service_id] = $_file;
		}
		$files = $buffers;

		/** Table sorting **/
		$params		= Request::$current->param();		
		$sorts		= array('asc', 'desc');		
		$sort		= isset($params['id2']) ? $this->id2 : 'added';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort => $order);
		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');	
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('subject'		=> 'Subject',
								'status'	=> 'Status',
								'added'		=> 'Added',
								'modified'	=> 'Modified');
		
		if (isset($params['id2']) && isset($params['id4'])) {
			$headers	= array_keys($table_headers);
			$sort		= (isset($params['id2']) && in_array(strtolower($params['id2']), $headers)) ? strtolower($params['id2']) : $headers[0];
			$order		= (isset($params['id4']) && in_array(strtolower($params['id4']), $sorts)) ? strtolower($params['id4']) : $sorts[0];
			$order_by	= array($sort=> $order);
		}
		if (isset($params['id3']) && isset($params['id5'])) {
			$headers	= array_keys($table_headers);
			$sort		= (isset($params['id3']) && in_array(strtolower($params['id3']), $headers)) ? strtolower($params['id3']) : $headers[0];
			$order		= (isset($params['id5']) && in_array(strtolower($params['id5']), $sorts)) ? strtolower($params['id5']) : $sorts[0];
			$order_by	= array($sort=> $order);
		}
		/** Execute list query **/
		$field			= isset($filters['f']) ? $filters['f'] : '';
		$keyword		= isset($filters['q']) ? $filters['q'] : '';
		$where_cond		= isset($where_cond) ? $where_cond : '';
		$total_rows		= count($this->services->find($where_cond));
		$total_record	= $total_rows;
		$listings		= $this->services->find($where_cond, $order_by, $per_page, $offset);
		
		$pagination		= Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 ));
		

		/** Views **/

		$content_vars		= array('listings'		=> $listings,
									'files'			=> $files,
									'total_record'	=> $total_record,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,	
									'search_keys'	=> $this->_search_keys,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

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
				if (isset($row_params['description']) && $row_params['description'])
					$fields[$row_name.'_description']	= '';
			}
		}
		$errors	= $fields;
		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);
			//$post->pre_filter('trim', 'name', 'subject');
			$post->rule('subject', 'not_empty');
			$post->rule('subject', 'min_length', array(':value', 4));
			$post->rule('name', 'not_empty');					
			$post->rule('name', array($this, '_safe_html_name'), array(':validation', ':field', 'name'));
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional']) {
						/*$post->add_rules($row_name, 'upload::required');*/
						//$post->add_rules($row_name, 'upload::valid');
					//}
					//if (isset($row_params['file_type']))
						//$post->add_rules($row_name, 'upload::type['.$row_params['file_type'].']');
					//if (isset($row_params['max_file_size']))
						//$post->add_rules($row_name, 'upload::size['.$row_params['max_file_size'].']');
				}
			}
			$post->rule('name', array($this, '_unique_name'), array(':validation', ':field', 'name'));
			if ($post->check()) {
				$fields	= $post->as_array();				
				$params	= array('name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'description'		=> $fields['description'],
								'user_id'			=> (isset($this->acl->logged_user->id)) ? $this->acl->logged_user->id : 0,
								'status'			=> $fields['status']);
				$id		= $this->services->add($params);
				if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						if (!Upload::not_empty($_FILES[$row_name]) || !Upload::type($_FILES[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($_FILES[$row_name]))
							continue;
						if (!File::exts_by_mime($post[$row_name]['type']))
							continue;
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($_FILES[$row_name]['name']);
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,0755);
						$file_data	= pathinfo($file_name);
						$file_mime	= $_FILES[$row_name]['type'];
						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params			= array('service_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '',
													'description'	=> isset($fields[$row_name.'_description']) ? $fields[$row_name.'_description'] : '');
							$this->file->add($params);
						}
					}
				}
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
					if ($row_val != '') {
						$buffers[$row_key]	= Lib::config('admin.error_field_open').ucfirst($row_val).Lib::config('admin.error_field_close');
					} else {
						$buffers[$row_key]	= $row_val;
					}
				}
				$errors		= $buffers;
			}
		}
		/** Generate Thumbnails **/
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		/** Views **/
		$where_cond			= array('status !='		=> 'deleted');
		$order_by			= array('order'			=> 'ASC');
		$orders				= $this->services->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'statuses'		=> $this->statuses,
									'class_name'	=> $this->_class_name,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}

	public function action_view () {
		$id = $this->id1;
		$this->services->id	= $id;
		if (!$this->services->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		/** Views **/
		
		$where_cond			= array('service_id'	=> $this->services->id);
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
		$content_vars		= array('services'		=> $this->services,
									'files'			=> $files,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									'upload_path'	=> $this->_upload_path,	
									'upload_url'	=> $this->_upload_url,	
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}

	public function action_edit () {
		$id = $this->id1;
		$category_id = $this->id2;
		$this->services->id	= $id;
		if (!$this->services->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array('name'				=> '',
						'subject'			=> '',
						'description'		=> '',
						'order'				=> '',
						'status'			=> '');
		if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
			foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				$fields[$row_name]	= '';
				if (isset($row_params['caption']) && $row_params['caption'])
					$fields[$row_name.'_caption']	= '';
				if (isset($row_params['description']) && $row_params['description'])
					$fields[$row_name.'_description']	= '';
			}
		}
		$errors	= $fields;
		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);
			//$post->pre_filter('trim', 'name', 'subject');
			//$post->pre_filter(array($this, '_safe_html_name'), 'name');
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					// if (isset($row_params['file_type']))
						// $post->add_rules($row_name, 'upload::type['.$row_params['file_type'].']');
					// if (isset($row_params['max_file_size']))
						// $post->add_rules($row_name, 'upload::size['.$row_params['max_file_size'].']');
					if (!File::exts_by_mime($post[$row_name]['type']))
						continue;	
				}
			}
			if ($post->check()) {
				$fields	= $post->as_array();
				$params	= array('name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'description'		=> $fields['description'],
								'status'			=> $fields['status']);
				foreach ($params as $var => $val) {
					$this->services->$var	= $val;
				}
				$this->services->update();
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('service_id'	=> $this->services->id);
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
						
						if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
							continue;
						
						//print_r($fields[$row_name.'_description']); exit();
						//print_r(Upload::not_empty($post[$row_name])); exit();
						//print_r($post[$row_name]['name']); exit();

						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);

						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,0777);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];

						
						if (!isset($files[$row_name])) {	
							$params			= array('service_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> isset($file_data['basename']) ? $file_data['basename'] : '',
													'file_type'		=> isset($file_mime) ? $file_mime : '',
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '',
													'description'	=> isset($fields[$row_name.'_description']) ? $fields[$row_name.'_description'] : '');
							$this->file->add($params);
						} else {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$params			= array('service_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> isset($file_data['basename']) ? $file_data['basename'] : $files[$row_name]->file_name,
													'file_type'		=> isset($file_mime) ? $file_mime : '',
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '',
													'description'	=> isset($fields[$row_name.'_description']) ? $fields[$row_name.'_description'] : '');		
							foreach ($params as $var => $val) {
								$this->file->$var	= $val;
							}
							$this->file->update();
						}
					}
				}
				
				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->services->id);
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
			$fields	= array('name'				=> $this->services->name,
							'subject'			=> $this->services->subject,
							'description'		=> $this->services->description,
							'order'				=> $this->services->order,
							'status'			=> $this->services->status);
			$where_cond			= array('service_id'	=> $this->services->id);
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
					if (isset($row_params['description']) && $row_params['description'])
						$fields[$row_name.'_description']	= (isset($files[$row_name])) ? $files[$row_name]->description : '';					
				}
			}
		}
		/** Views **/
		$where_cond			= array('status !='		=> 'deleted');
		$order_by			= array('order'			=> 'ASC');
		$orders				= $this->services->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,	
									'upload_url'	=> $this->_upload_url,	
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'services'		=> $this->services,
									'files'			=> $files,
									'orders'		=> $orders,
									'statuses'		=> $this->statuses,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'));
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content;
	}

    public function action_delete() {
		$this->services->id	= $this->id1;
		if (!$this->services->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
			// This setting is used for deleting all file included
			if ($this->services->delete($this->id1)) {
				$where_cond = array('service_id'=>$this->id1);
				$mediafiles = $this->file->find($where_cond);
				foreach ($mediafiles as $files){
					if (is_readable($this->_upload_path.$files->file_name)) {
						@unlink($this->_upload_path.$files->file_name);
						 $this->file->delete($files->id);
					}
				}
				echo 1;
			} else {
				echo 0;
			}	
		} else {
			$this->redirect(ADMIN.$this->request->controller());
		}
		exit();
    }
	
	// Action for download item status	
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
				$this->services->id	= $row;

				if (!$this->services->load())
					continue;

				$this->services->status	= $this->request->post('select_action');
				$this->services->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN.$this->_class_name);
			
		}
		
	}
	
	/** CALLBACKS **/

	public function _unique_name (Validation $array, $field) {
		if (isset($this->services->name) && $this->services->name == $array[$field])
			return;

		$where_cond		= array('name'	=> $array[$field]);
		$name_exists	= ($this->services->find_count($where_cond) != 0);

		if ($name_exists)
			$array->error($field, 'name_exists');
	}

	public function _valid_user_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;

		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->user->find_count($where_cond) != 0);

		if (!$parent_exists)
			$array->error($field, 'invalid_user_id');
	}

	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->error($field, 'invalid_status');
	}
	
	/** CALLBACKS **/
	public function _valid_category_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;
		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->category->find_count($where_cond) != 0);
		if (!$parent_exists)
			$array->error($field, 'invalid_category_id');
	}
	
	public function _valid_search_key ($value = '') {
		return mysql_real_escape_string($value);
	}
	
	/** PRE FILTER **/
	public function _safe_html_name ($value = '') {
		//return htmlentities($value);
		return $value;
	}
	
	public function _reverse_date ($value = '') {
		if (strpos($value, '/') != 0)
			return (implode('-', array_reverse(explode('/', $value))));
		else
			return (implode('/', array_reverse(explode('-', $value))));
	}
	
	public function _category_id ($value = '') {
		$where_cond	= array('name LIKE'	=> '%'.$value.'%',
							'status !='	=> 'deleted');
		$buffers	= $this->category->find($where_cond);
		$ids		= array();
		foreach ($buffers as $row) {
			$ids[]	= $row->id;
		}
		return $ids;
	}
}
