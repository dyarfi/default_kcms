<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_MediaRelease extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_current_menu;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

	protected $media;
	protected $medias;
	protected $mediarelease;
	protected $file;
	protected $statuses;

	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_search_keys = array('name' => 'Name', 'subject' => 'Subject', 'description' => 'Description', 'status' => 'Status');

		$this->_module_name = 'media';
		$this->_class_name	= $this->controller;
		$this->_module_menu	= $this->acl->module_menu;
				
		$this->_prefilter_keys = array('media_id');
		
		$this->_prefs = (Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->_upload_path = (Lib::config($this->_module_name.'.upload_path') !== NULL) ? Lib::config($this->_module_name.'.upload_path') : array();
		
		$this->_upload_url = (Lib::config($this->_module_name.'.upload_url') !== NULL) ? Lib::config($this->_module_name.'.upload_url') : array();

		
		$this->mediarelease	= new Model_MediaRelease;
		$this->file			= new Model_MediaReleaseFile;
		
		$this->statuses = array('publish', 'unpublish');   
		//if($this->session->get('level_id'.ADMIN_SESSION) <= 2)
		//$this->statuses		= array_merge($this->statuses, array('deleted'));		
		
		unset($buffers);
	}
	public function action_index () {	

		$where_cond	= array('status !='	=> 'deleted');
		
		/** Find & Multiple change status **/
		if ($_POST) {			
			$post	= new Validation($_POST);
			if (isset($post['field']) || isset($post['keyword'])) {
				$post->rule('keyword', 'regex', array(':value', '/^[a-z0-9_.\s\-]++$/iD'));
				
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
			
			//if ($media_id != '')			
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
//									'media_id'		=> 'Category',
									'description'	=> 'Description',
									'status'		=> 'Status',
									'added'			=> 'Added',
									'modified'		=> 'Modified');
			
			if (isset($params['sort']) && isset($params['order'])) {
				$headers	= array_keys($table_headers);
				$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
				$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];
				$order_by	= array($sort=> $order);
				
				if ($media_id != '')
					$base_url	= ADMIN.$this->_class_name.'/index/'.$media_id.'/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
					else
						$base_url	= ADMIN.$this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
			}		
			
			/** Execute list query **/
			$field			= isset($filters['f']) ? $filters['f'] : '';
			$keyword		= isset($filters['q']) ? $filters['q'] : '';
			$where_cond		= isset($where_cond) ? $where_cond : '';
			$total_rows		= count($this->mediarelease->find($where_cond));
			$total_record	= $total_rows;
			$listings	= $this->mediarelease->find($where_cond, $order_by, $per_page, $offset);
			$pagination = Pagination::factory(array(
					'total_items' 		=> $total_rows,
					'items_per_page' 	=> $per_page,
				)
			);	
			
			/** Views **/
			$content_vars = array('listings'	=> $listings,
								'total_record'	=> $total_record,
								'table_headers'	=> $table_headers,
								'statuses'		=> $this->statuses,
								'search_keys'	=> $this->_search_keys,
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
								'class_name'	=> $this->_class_name,
								'class_name'	=> $this->_class_name);
			$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');
			
			foreach ($content_vars as $var => $val) {
				$content->$var	= $val;		
			}
			
			$this->template->content		= $content; 	
			
		}
		public function action_add () {	
			$media_id	= $this->id1;
			$fields	= array('subject'		=> '',
							'name'			=> '',
							'description'	=> '',
							'status'		=> '');
			
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					$fields[$row_name]	= '';

					if (isset($row_params['caption']) && $row_params['caption'])
						$fields[$row_name.'_caption']	= '';
				}
			}
	
			$errors	= $fields;
			$fields['media_id']	= $media_id;	
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
					$params	= array('subject'		=> $fields['subject'],
									'name'			=> $fields['name'],
									'description'	=> $fields['description'],
									'user_id'		=> (isset($this->acl->logged_user->id)) ? $this->acl->logged_user->id : 0,
								
									'status'		=> $fields['status']);
					$id		= $this->mediarelease->add($params);
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
									$params = array('release_id'	 => $id,
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
									'medias'	=> $this->medias,
									'module_menu'	=> $this->_module_menu,
									'class_name' 	=> $this->_class_name,
									'class_name' 	=> $this->_class_name);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;	
		}
		
		$this->template->content		= $content;
	}
	public function action_view () {
		$id = $this->id1;

		$this->mediarelease->id	= $id;		
		if (!$this->mediarelease->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;		
		}
		
		$where_cond			= array('release_id'	=> $this->mediarelease->id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();

		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}

		$files				= $buffers;

		unset($buffers);
		
		/** Generate Thumbnails **/
		
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
	
		
		$content_vars		= array(
									'files'	  => $files,
									'mediarelease'	=> $this->mediarelease,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime')									);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_view');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;	
		}
		
		$this->template->content		= $content;     
	}	
	public function action_edit () {
		$id = $this->id1;
		$category_id = $this->id2;
		$this->mediarelease->id	= $id;
		if (!$this->mediarelease->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array('category_id'	=> '',
						'subject'		=> '',
						'name'			=> '',
						'description'	=> '',
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
								'status'		=> $fields['status']);
				foreach ($params as $var => $val) {
					$this->mediarelease->$var	= $val;
				}
				
				$this->mediarelease->update();
				
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('release_id'	=> $this->mediarelease->id);
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
						
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0777);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						
						if (!isset($files[$row_name])) {
							$params			= array('release_id'	=> $this->mediarelease->id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							
							$this->file->add($params);
						} else {
						
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();

							$params			= array('release_id'	=> $this->mediarelease->id,
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
				if ($category_id != '')
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->mediarelease->id.'/'.$category_id);
				else
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->mediarelease->id); return;
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
			$fields	= array('subject'			=> $this->mediarelease->subject,
							'name'				=> $this->mediarelease->name,
							'description'		=> $this->mediarelease->description,
							'status'			=> $this->mediarelease->status);
			
			$where_cond			= array('release_id'	=> $this->mediarelease->id);
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
		//$orders				= $this->media->find($where_cond, $order_by);
		
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'mediarelease'	=> $this->mediarelease,
									'medias'		=> $this->medias,
									'files'			=> $files,
									'statuses'		=> $this->statuses,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'statuses'		=> $this->statuses,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'));
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;		
		}
		
		$this->template->content		= $content;	
	}
	public function action_delete () {
		$this->mediarelease->id	= $this->id1;
		if (!$this->mediarelease->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		/*
		$this->mediarelease->status	= 'deleted';
		if($this->mediarelease->update())
			echo $this->id2;
		else
			echo 0;
		exit;
		 * 
		 */
		
		if ($this->request->is_ajax()) {
		
			// Set is_deleted / status to TRUE
			// $this->media->status	= 'deleted';
			// if($this->media->update())
				//echo $this->id2;
			//else
				//echo 0;	
			
			// This setting is used for deleting all file included
			if (Model_MediaRelease::instance()->delete($this->id1)) {
				$where_cond = array('release_id'=>$this->id1);
				$mediafiles = Model_MediaReleaseFile::instance()->find($where_cond);

				foreach ($mediafiles as $files){
					if (file_exists($this->_upload_path.$files->file_name)) {
						Model_MediaReleaseFile::instance()->delete($files->id);
						unlink($this->_upload_path.$files->file_name);
					}
				}
				
				//echo $this->id2;
				echo 1;
			} else {
				echo 0;
			}	
			
		} else {
			$this->redirect(ADMIN.$this->request->controller());
			//die();
		}

		exit();
		//		$this->session->set_flash('function_delete', 'success');////		
		//$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';
		//		$this->redirect($redirect_url);	
	}	
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				$this->mediarelease->id	= $row;
				if (!$this->mediarelease->load())
					continue;
				$this->mediarelease->status	= $this->request->post('select_action');
				$this->mediarelease->update();
			}
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);
		} else {
			$this->redirect(ADMIN.$this->_class_name);	
		}
	}
	
	public function action_download() {
		/*
		$files		= $this->id1;
		
		$where_cond	= array('file_name'	=> $files);

		$files		= $this->file->find($where_cond);

		foreach ($files as $row) {
			Lib::_download_file_force('',$this->_upload_url.$row->file_name);
		}
		 */
		if (!$this->id1)
			return;
		
		// Decode Filenames	
		$content  = explode("||",base64_decode($this->id1));
		// Get raw file
		$fileraw = $content[1];
		// Get file extension
		$filexts  = strstr($content[1],'.');
		// Get file name
		$filename = str_replace(' ', '_', trim($content[0] . $filexts));

		// Return File Force Download Helper
		return Lib::_download_file_force($filename,$fileraw);
	}
	
	/** CALLBACKS **/	
	public function _valid_media_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;
		$where_cond		= array('id'	=> $array[$field]);		
		$parent_exists	= ($this->category->find_count($where_cond) != 0);
		if (!$parent_exists)
			$array->error($field, 'invalid_media_id');	
	}	
	public function _unique_name (Validation $array, $field) {		
		if (isset($this->mediarelease->name) && $this->mediarelease->name == $array[$field])
			return;	
		$where_cond		= array('name'	=> $array[$field]);		
		$name_exists	= ! $this->mediarelease->find_count($where_cond);
		
		if (!$name_exists)
			$array->error($field, 'name_exists');	
	}
	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->error($field, 'invalid_status');		
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