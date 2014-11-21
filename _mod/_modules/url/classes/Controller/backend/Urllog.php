<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_UrlLog extends Controller_Backend_BaseAdmin {
    
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
	protected $_upload_path;
	protected $_upload_url;
	
	protected $urllog;
    protected $_users;
	protected $_uid;
    
	public function before () {
		// Get parent before method
        parent::before();
		
        $this->urlLog   = new Model_UrlLog;
		$this->user		= new Model_User();
        
		$this->_module_name		= 'url';				
		$this->_class_name		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;
				
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();

		$this->_search_keys		= array('shorturl'		=> 'Shorturl',
                                        'referrer'		=> 'Referrer',
                                        'user_agent'    => 'User Agent',
										'ip_address'	=> 'Ip Address');
	
		$users					= $this->user->find();
		
		$buffers				= array();
		foreach ($users as $user){
			$buffers[$user->id] = $user;
		}
		$this->_users			= $buffers;		
		unset($buffers);
		
		//-- User id from user login session 'user_id'
		$this->_uid				= $this->session->get('user_id');
		
		//-- Default urllog statuses
		$this->statuses			= array('publish','unpublish');
		
    }
    
    public function action_main() { $this->template->content = 'Default News'; }
    
    public function action_index() {       
		//$where_cond	= array('status' => 'publish');
		//$this->urlLog->find($where_cond);
		//exit();
		//$order_by	= array('added' => 'DESC');

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
		$order		= isset($params['id4']) ? $this->id4 : $sorts[1];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN . $this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('click_time'		=> 'Click Time',
								'shorturl'			=> 'Shorturl',
								'referrer'			=> 'Referrer',
								'user_agent'		=> 'User Agent',
								'ip_address'		=> 'Ip Address');

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

		//$total_rows	= $this->urlLog->find_count($where_cond);
		
		$total_rows		= $this->urlLog->find_count($where_cond);
		$total_record 	= $total_rows;
		
		//$listings	= $this->urlLog->find($where_cond, array_merge($order_by, array('subject'=>'asc')), $per_page, $offset);
		
		$listings	= $this->urlLog->find($where_cond, $order_by, $per_page, $offset);
		
		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);
			//$this->redirect($base_url.$page_index);
			//return;
		}

		$this->session->get($this->_module_name.'_index', $base_url.$page_index);

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
									'search_keys'	=> $this->_search_keys,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
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
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
    }
    
    public function action_add() {

		$fields	= array(
						'division_id'   => '',
						'name'		=> '',
						'subject'	=> '',
						'synopsis'	=> '',
						'urllog_date'	=> '',
						'end_date'	=> '',
						'synopsis'	=> '',
						'qualification'	=> '',
						'text'	=> '',
						'status'	=> '');

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
			
                $post->rule('subject', 'not_empty');
                $post->rule('subject', 'min_length', array(':value', 4));
                $post->rule('name', 'not_empty');
                $post->rule('name', 'min_length', array(':value', 4));
                $post->rule('urllog_date', 'not_empty');
                $post->rule('text', 'not_empty');
                $post->rule('text', 'min_length', array(':value', 4));
                $post->rule('status', 'not_empty');
                
                $post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
            
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					if (isset($row_params['optional']) && !$row_params['optional']) {
						//$post->add_rules($row_name, 'upload::required');
						//$post->rule($row_name, 'upload::valid');
					}
					
					if (Upload::type($post[$row_name],explode(',',$row_params['file_type'])) !== 1)
						continue;				

					//print_r($post); exit();
					//$post->rule(substr($post[$row_name]['type'], strpos($post[$row_name]['type'], '/') + 1), 'upload::type['.$row_params['file_type'].']');

					//if (isset($row_params['max_file_size']))
						//$post->rule(round($post[$row_name]['size'] / 1024, 2).'KB', 'upload::size['.$row_params['max_file_size'].']');
				}
			}

			if ($post->check()) {
				$fields	= $post->as_array();

				$params	= array(
						'name'			=> $fields['name'],
						'subject'		=> $fields['subject'],
						'synopsis'		=> $fields['synopsis'],
						'urllog_date'		=> $fields['urllog_date'],
						'synopsis'		=> $fields['synopsis'],
                        'text'          => $fields['text'],
						'user_id'		=> (isset($this->acl->user->id)) ? $this->acl->user->id : 0,
						'status'		=> $fields['status']);

				$id		= $this->urlLog->add($params);
				//$id 	= 9;
				if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						//if (!upload::required($_FILES[$row_name]) || !File::mime_by_ext($_FILES[$row_name]))
							//continue;

						
						//print_r(!Upload::type($_FILES[$row_name],explode(',',$row_name['file_type']))); exit();
						//print_r(!File::ext_by_mime('image/jpeg')); exit();
						
						if (!File::exts_by_mime($post[$row_name]['type']))
							continue;
						
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,0777);
								
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];

						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params = array('urllog_id'	 => $id,
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
					$this->redirect(ADMIN . $this->_class_name.'/add');
					return;
				}

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$id);
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

		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'statuses'	=> $this->statuses,
									'class_name' 	=> $this->_class_name);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
    }
    
	public function action_edit () {
		$this->urlLog->id	= $this->id1;

		if (!$this->urlLog->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/error/invalid_request');
			return;
		}

		$fields	= array(
						'name'				=> '',
						'subject'			=> '',
						/*'type'				=> '',*/
						'urllog_date'			=> '',
						'synopsis'			=> '',
						'text'				=> '',
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
			$post->rule('name', 'min_length', array(':value', 4));
            $post->rule('urllog_date', 'not_empty');
			$post->rule('text', 'not_empty');
            $post->rule('text', 'min_length', array(':value', 4));
            $post->rule('status', 'not_empty');
			
			//$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));

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

//			$post->add_callbacks('name', array($this, '_unique_name'));

			if ($post->check()) {
				$fields	= $post->as_array();
	
				//$fields['name']	= $this->_remove_tag($fields['name']);

				$params	= array(
								'name'		=> $fields['name'],
								'subject'	=> $fields['subject'],
								/*'type'	=> $fields['type'],*/
								//'urllog_date'	=> $this->_reverse_date($fields['urllog_date']),
								'urllog_date'	=> $fields['urllog_date'],
								'synopsis'	=> isset($fields['synopsis']) ? $fields['synopsis'] : '',
								'text'		=> $fields['text'],
								'status'	=> $fields['status'],
								'user_id'	=> !empty($fields['user_id']) ? $fields['user_id'] : $this->_uid
						);

				foreach ($params as $var => $val) {
					$this->urlLog->$var	= $val;
				}

				$this->urlLog->update();

				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('urllog_id'	=> $this->urlLog->id);
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
						
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,'0777');
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						
						if (!isset($files[$row_name])) {
							$params			= array('urllog_id'		=> $this->urlLog->id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							
							$this->file->add($params);
						} else {
						
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();

							$params			= array('urllog_id'		=> $this->urlLog->id,
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

				$this->redirect(ADMIN . $this->_class_name.'/view/'.$this->urlLog->id);
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
							'name'				=> $this->urlLog->name,
							'subject'			=> $this->urlLog->subject,
							'urllog_date'			=> $this->urlLog->urllog_date,
							'synopsis'			=> $this->urlLog->synopsis,
							'text'				=> $this->urlLog->text,
							'status'			=> $this->urlLog->status);

			$where_cond			= array('urllog_id'	=> $this->urlLog->id);
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

		/** Generate Thumbnails **/
		
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		
		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'class_name'	=> $this->_class_name,
									'users'			=> $this->_users,
									'urllog'			=> $this->urlLog,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'files'			=> !empty($files) ? $files : '' ,
									'events'		=> $this->urlLog->find(array('status !=' => 'deleted', 'type' => 'event')),
									'statuses'		=> $this->statuses,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'));

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;
	}
    
	public function action_download() {
		$files		= $this->id1;
		
		$where_cond	= array('file_name'	=> $files);

		$files		= $this->file->find($where_cond);

		foreach ($files as $row) {
			Lib::_download(Lib::config($this->_class_name.'.upload_url').$row->file_name);
		}
	}
	
    public function action_view() {

		$this->urlLog->id	= $this->id1;
		
		if (!$this->urlLog->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}
		
		$where_cond			= array('urllog_id'	=> $this->urlLog->id);
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
									'files'	  => $files,
									'urllog'	  => $this->urlLog,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,	
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime')
									);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->page_title		= Lib::config('admin.title');
		$this->template->content		= $content; 
    }
    
    public function action_delete() {
	
		$this->urlLog->id	= $this->id1;

		if (!$this->urlLog->load()) {
			$this->redirect(ADMIN . $this->_class_name.'/index');
			return;
		}
		
		// Set is_deleted / status to TRUE
		// $this->urlLog->status	= 'deleted';
		
		// if($this->urlLog->update())
			// echo 1;
		// else
			// echo 0;
			
		
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
		
			// Set is_deleted / status to TRUE
			//$this->urlLog->status	= 'deleted';
			//if($this->urlLog->update())
				//echo 1;
			//else
				//echo 0;	
			
			if($this->urlLog->delete($this->id1)) {
				echo 1;
			} else {
				echo 0;
			} 
				
			
				
		} else {
			$this->redirect(ADMIN . $this->request->controller());
			//die();
		}

		exit();
    }
    
	
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->urlLog->id	= $row;

				if (!$this->urlLog->load())
					continue;

				$this->urlLog->status	= $this->request->post('select_action');
				$this->urlLog->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url, ADMIN)) ? $this->acl->previous_url : ADMIN . $this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN . $this->_class_name);
			
		}
		
	}

} // End Event
