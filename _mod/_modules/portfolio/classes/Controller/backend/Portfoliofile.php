<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_PortfolioFile extends Controller_Backend_BaseAdmin {

	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;	
	
	protected $file;
	protected $portfolio;
	protected $portfolios;
	protected $statuses;
	
	public function before () {
		// Get parent before method
        parent::before();
		
		$portfolio				= new Model_Portfolio;

		$where_cond			= array('status !='	=> 'deleted', 'status !='	=> 'unpublish');

		$portfolios				= $portfolio->find($where_cond);
		$buffers			= array();

		$this->_module_name	= 'portfolio';
		$this->_class_name	= $this->controller;	
		$this->_module_menu	= $this->acl->module_menu;
		
		$this->_search_keys	= array('name'		=> 'Name',
									'title'		=> 'Title',
									'status'	=> 'Status');
		
		$this->_prefilter_keys	= array('portfolio_id');
		$this->_prefs		= (Lib::config($this->_module_name.'.'.$this->_class_name .'_fields') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name .'_fields') : array();
		
		$this->_prefs		= array_merge($this->_prefs, (Lib::config('site.'.$this->_class_name.'_fields') !== NULL) ? Lib::config('site.'.$this->_class_name.'_fields') : array());
		
		$this->_upload_path = (Lib::config($this->_module_name.'.portfoliofile_upload_path') !== NULL) ? Lib::config($this->_module_name.'.portfoliofile_upload_path') : array();
		$this->_upload_url = (Lib::config($this->_module_name.'.portfoliofile_upload_url') !== NULL) ? Lib::config($this->_module_name.'.portfoliofile_upload_url') : array();
		
		$this->file				= new Model_PortfolioFilesFile;
		$this->portfolio		= new Model_Portfolio;
		
		$this->portfolios		= $buffers;

		$this->statuses		= array('publish',
									'unpublish');

		unset($portfolio, $portfolios, $buffers);
	}

	function action_index () {
		
		$portfolio_id = $this->id1;
		
		if ($portfolio_id != '')
			$this->_prefs	= array_merge($this->_prefs, (Lib::config('site.'.$this->_class_name.'_'.$portfolio_id.'_index_fields') !== NULL) ? Lib::config('site.'.$this->_class_name.'_'.$portfolio_id.'_index_fields') : array());

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

		/** Table sorting **/

		$params		= Request::$current->param();
		$sorts		= array('asc', 'desc');

		$sort		= isset($params['sort']) ? $params['sort'] : 'name';
		$order		= isset($params['order']) ? $params['order'] : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($params['page']) ? $params['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		$page_url	= '';
		$base_url	= ADMIN.$this->_class_name.'/index/page/';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('title'		=> 'Title',
								'portfolio_id'	=> 'Portfolio',
								'status'	=> 'Status',
								'added'		=> 'Added',
								'modified'	=> 'Modified');

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

		if ($portfolio_id != '' && (($this->portfolio = $this->portfolio->load($portfolio_id)) || ($this->portfolio = Model_Portfolio::instance()->load_by_name($portfolio_id))))
			$portfolio_id	= $this->portfolio->id;
		else
			$portfolio_id	= '';

		if ($portfolio_id != '' && is_array($where_cond))
			$where_cond	= array_merge(array('portfolio_id' => $portfolio_id), $where_cond);

		$total_rows		= count($this->file->find($where_cond));
		$total_record	= $total_rows;
		$listings		= $this->file->find($where_cond, $order_by, $per_page, $offset);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);

		$portfolios				= $this->portfolio->find(array('status'=>'publish'));

		foreach ($portfolios as $row) {
			$buffers[$row->id]	= $row;
		}
		
		/** Generate Thumbnails **/

		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);

		/** Views **/
		
		$content_vars		= array('listings'		=> $listings,
									'total_record'	=> $total_record,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'search_keys'	=> $this->_search_keys,
									'upload_url'	=> $this->_upload_url,
									'upload_path'	=> $this->_upload_path,		
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'portfolios'	=> $this->portfolios,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime'),
									'mime_icon'		=> Lib::config($this->_module_name.'.mime_icon'),
									'portfolio_id'	=> $portfolio_id);

		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_add () {
		
		$portfolio_id = $this->id1;
		
		$fields	= array('portfolio_id'			=> '',
						'name'				=> '',
						'title'				=> '',
						'description'		=> '',
						'caption'			=> '',
						'allow_comment'		=> (isset($this->_prefs['show_allow_comment']) && $this->_prefs['show_allow_comment']) ? '' : 0,
						'status'			=> '');

		if (isset($this->_prefs['uploads'])) {
			foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				$fields[$row_name]	= '';

				if (isset($row_params['caption']) && $row_params['caption'])
					$fields[$row_name.'_caption']	= '';
			}
		}

		$errors	= $fields;

		if ($portfolio_id != '' && (($this->portfolio = Model_Portfolio::instance()->load($portfolio_id)) || ($this->portfolio = Model_Portfolio::instance()->load_by_name($portfolio_id))))
			$fields['portfolio_id']	= $this->portfolio->id;

		if ($_POST) {
			if ($_FILES) 
				$post	= Validation::factory(array_merge($_POST,$_FILES));
			else
				$post	= Validation::factory($_POST);

			//print_r($post); exit();
			//$post->pre_filter('trim', 'name', 'subject');
			//$post->pre_filter('intval', 'portfolio_id');

			$post->rule('portfolio_id', 'not_empty');
			$post->rule('name', 'not_empty');
			$post->rule('title', 'not_empty');
			$post->rule('status', 'not_empty');

			if (isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					// Set if this is optional or not, return true
					if (!isset($row_params['optional'])) {
						//$post->rule($row_name, 'Upload::not_empty');
					}
					// Set if this is not the right file type, return true
					if (isset($row_params['file_type'])) {
						//$post->rule($row_name, 'Upload::type', array(':value', array($row_params['file_type'])));
					} 
					// Set if this is not the right max file size, return true
					if (isset($row_params['max_file_size'])) {
						//$post->rule($row_name, 'Upload::size', array(':value', $row_params['max_file_size']));
					} 
					// Set if this file has valid name in database, return true
					if (isset($post[$row_name]['name'])) {
						//$post->rule($row_name, array($this, '_unique_filename'), array(':validation', $row_name));
					}
				}
			}

			$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
			$post->rule('portfolio_id', array($this, '_valid_portfolio_id'), array(':validation', 'portfolio_id'));
			$post->rule('status', array($this, '_valid_status'), array(':validation', 'status'));

			if ($post->check()) {
				$fields	= $post->as_array();

				//print_r($this->_prefs['uploads']);
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				//if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
					//continue;
					
					if (!$post[$row_name]['name'])
						continue;
					
					$file_data	= pathinfo($post[$row_name]['name']);
					if ($this->_prefs['show_filename'] == TRUE) {
						// This will provide real filename
						$filename_upload	= preg_replace('/\.[^.]*$/', '', $file_data['basename']);
						// This will provide real filename based on Upload::save but with addition random char
						// $filename_upload	= NULL;
					}
					else {
						$filename_upload	= md5(time() + rand(100, 999)) . '.' . $file_data['extension'];
					}
					
					$file_name	= Lib::_upload_to($post[$row_name], $filename_upload.'.'.$file_data['extension'], $this->_upload_path, 0755);
					$file_data	= pathinfo($file_name);
					$file_mime	= $post[$row_name]['type'];
					if ($file_name != '') {
						$params			= array('portfolio_id'		=> $fields['portfolio_id'],
												'name'			=> $fields['name'],
												'title'			=> $fields['title'],
												'description'	=> $fields['description'],
												'field_name'	=> $row_name,
												'file_name'		=> $file_data['basename'],
												'file_type'		=> $file_mime,
												'caption'		=> $fields[$row_name.'_caption'],
												'allow_comment'	=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
												'status'		=> $fields['status']);
						$id = $this->file->add($params);
					}
				}

				if (isset($id)) {
					if (isset($fields['add_another'])) {
						$this->redirect(ADMIN.$this->_class_name.'/add');
						return;
					}

					$this->redirect(ADMIN.$this->_class_name.'/view/'.$id);
					return;
				}
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
									'portfolios'	=> $this->portfolios,	
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
	}

	public function action_view () {
		$id = $this->id1;
		$this->file->id	= $id;

		if (!$this->file->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		/** Views **/

		if ($this->file->portfolio_id != 0) {
			$portfolio			= $this->portfolio->load($this->file->portfolio_id);
			$portfolio			= HTML::chars($portfolio->subject, TRUE);
		} else {
			$portfolio			= 'This file doesn\'t have portfolio';
		}

		/** Generate Thumbnails **/

		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);

		$content_vars		= array('file'			=> $this->file,
									'portfolio'		=> $portfolio,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'upload_url'	=> $this->_upload_url,
									'upload_path'	=> $this->_upload_path,		
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime'),
									'mime_icon'		=> Lib::config($this->_module_name.'.mime_icon'));

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
	}

	public function action_edit () {
		$id = $this->id1;
		$this->file->id	= $id;

		if (!$this->file->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		$fields	= array('portfolio_id'			=> '',
						'name'				=> '',
						'title'				=> '',
						'description'		=> '',
						'caption'			=> '',
						'allow_comment'		=> (isset($this->_prefs['show_allow_comment']) && $this->_prefs['show_allow_comment']) ? '' : 0,
						'status'			=> '');

		if (isset($this->_prefs['uploads'])) {
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

			//$post->pre_filter('trim', 'name', 'subject');
			//$post->pre_filter('intval', 'portfolio_id');

			$post->rule('portfolio_id', 'not_empty');
			$post->rule('name', 'min_length', array(':value', 4));
			$post->rule('title', 'not_empty');
			$post->rule('status', 'not_empty');

			if (isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional'])
						//$post->rule($row_name, 'upload::valid');

					//if (isset($row_params['file_type']))
						//$post->rule($row_name, 'upload::type['.$row_params['file_type'].']');

					//if (isset($row_params['max_file_size']))
						//$post->rule($row_name, 'upload::size['.$row_params['max_file_size'].']');
					
					if (!File::exts_by_mime($post[$row_name]['type']))
						continue;
				}
			}

			$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
			$post->rule('portfolio_id', array($this, '_valid_portfolio_id'), array(':validation', 'portfolio_id'));
			//$post->rule('status', array($this, '_valid_status'), array(':validation', 'status'));
			
			if ($post->check()) {
				$fields	= $post->as_array();

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
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,0777);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						
						if (!isset($files[$row_name])) {
							$params			= array('portfolio_id'		=> $fields['portfolio_id'],
													'name'			=> $fields['name'],
													'title'			=> $fields['title'],
													'description'	=> $fields['description'],
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

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->file->id);
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
				
				//print_r($errors); exit();
			}
		} else {
			$fields	= array('portfolio_id'			=> $this->file->portfolio_id,
							'name'				=> $this->file->name,
							'title'				=> $this->file->title,
							'description'		=> $this->file->description,
							'caption'			=> $this->file->caption,
							'allow_comment'		=> (isset($this->_prefs['show_allow_comment']) && $this->_prefs['show_allow_comment'] && $this->file->allow_comment == 1) ? 1 : 0,
							'status'			=> $this->file->status);

			if (isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					$fields[$row_name]	= '';

					if (isset($row_params['caption']) && $row_params['caption'])
						$fields[$row_name.'_caption']	= $this->file->caption;
				}
			}
		}

		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'file'			=> $this->file,
									'portfolios'		=> $this->portfolios,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'upload_url'	=> $this->_upload_url,
									'upload_path'	=> $this->_upload_path,	
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime'));

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
	}

	public function action_delete () {
		$id = '';
		if (Request::$current->query('file') !== '' && $this->id1 === '') {
			$_files = $this->file->find(array('file_name'=>Request::$current->query('file')));
			$id = $_files[0]->id;
		} else {
			$id = $this->id1;
		}
		
		$this->file->id	= $id;

		// This is used for only Ajax Request		
		if ($this->request->is_ajax() && Request::$current->query('file') == '') {			
			// This setting is used for deleting all file included
			$files = $this->file->load($id);
			if (!empty($files)) {
				if (is_readable($this->_upload_path.$files->file_name)) {
					@unlink($this->_upload_path.$files->file_name);
					$this->file->delete($id);
				} else {
					$this->file->delete($id);
				}				
				echo 1;
			} else {
				echo 0;
			}
		} else if (Request::$current->query('file')) {
			// This setting is used for deleting all file included
			$files = $this->file->load($id);
			if (!empty($files)) {
				if (is_readable($this->_upload_path.$files->file_name)) {
					@unlink($this->_upload_path.$files->file_name);
					$this->file->delete($id);
				} else {
					$this->file->delete($id);
				}
				
				echo 1;
			} else {
				echo 0;
			}	
		} else {	
			
		}
		
		exit;
	}
	
	// Action for update item status
	public function action_change() {
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->file->id	= $row;

				if (!$this->file->load())
					continue;

				$this->file->status	= $this->request->post('select_action');
				$this->file->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN.$this->_class_name);
			
		}
		
	}
	
	public function action_upload (){
	
		if ($this->request->is_ajax()) {
			if ($_FILES) {			
				foreach ($_FILES as $files) {
					$this->file->add(array(
									'portfolio_id'	=> $this->id1,
									'name'			=> $files['name'],
									'file_name'		=> $files['name'],
									'file_type'		=> $files['type'],
									'field_name'	=> 'image_1',
									'status'		=> 'publish'));
				}
				
				$options = array('script_url' => URL::site(ADMIN . $this->_class_name.'/delete/'),
								 'upload_dir' => $this->_upload_path,	
								 'upload_url' => $this->_upload_url,
								 'thumbnail'  => array('max_width' => 287, 'max_height' => 315),
								 'mkdir_mode' => 0777);
				
				$upload_handler = new UploadHandler($options);				
				
				echo $upload_handler;
				
				/** Generate Thumbnails **/

				Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		
				exit;

			}
			
			$this->template     = View::factory('themes/defaultblank');
			
			$content_vars		= array('file'			=> $this->file,
										'statuses'		=> $this->statuses,
										'module_menu'	=> $this->_module_menu,				
										'class_name'	=> $this->_class_name,
										'upload_url'	=> $this->_upload_url,
										'upload_path'	=> $this->_upload_path);

			$content_vars		= array_merge($content_vars, $this->_prefs);

			$content			= View::factory($this->_module_name.'/backend/galleryfile_upload');

			foreach ($content_vars as $var => $val) {
				$content->$var	= $val;
			}
			$this->template->content	= $content;
			
			
			
			echo $this->template;
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
		
		// Retrieve Raw File
		$fileraw = base64_decode($this->id1);
		// Set filename to original
		$filename = '';
		
		//return Lib::_download_file_force($filename,$fileraw);
		return Lib::_download($fileraw);
	}
	/** CALLBACKS **/
	public function _unique_filename (Validation $array, $field) {
		if (!isset($array[$field]['name']))
			return;
		$filename = $this->file->load_by_filename(preg_replace('/\s+/u', '_', $array[$field]['name']));
		if ($filename)
			return $array->error($field, 'unique_filename');
	}
	
	public function _unique_name (Validation $array, $field) {
		if (isset($this->file->name) && $this->file->name == $array[$field])
			return;

		$where_cond		= array('name'	=> $array[$field]);
		$name_exists	= ($this->file->find_count($where_cond) != 0);

		if ($name_exists)
			$array->error($field, 'name_exists');
	}

	public function _valid_portfolio_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;

		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->portfolio->find_count($where_cond) != 0);

		if (!$parent_exists)
			$array->error($field, 'invalid_portfolio_id');
	}

	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->error($field, 'invalid_status');
	}
}
