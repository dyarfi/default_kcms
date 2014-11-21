<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_DownloadFile extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $file;
	protected $downloadtype;
	protected $downloadtypes;
	protected $statuses;

	public function before () {
		parent::before();

		$downloadtype		= new Model_DownloadType;
		$where_cond			= array('status !='	=> 'deleted', 'status !='	=> 'unpublish');
		$downloadtypes		= $downloadtype->find($where_cond);
				
		$buffers			= array();
		$buffers_release	= array();

		$this->_module_name	= 'download';
		$this->_class_name	= strtolower(str_replace('Controller_Backend_', '', get_class($this)));
		$this->_module_menu		= $this->acl->module_menu;
				
		$this->_search_keys	= array(
									'title'			=> 'Title',
									'name'			=> 'Name',
									'description'	=> 'Description',
									'status'		=> 'Status');
		
		$this->_prefs		= Lib::config($this->_module_name.'.'.$this->_class_name.'_fields');
		
		$this->_upload_path	= (Lib::config($this->_module_name.'.downloadfile_upload_path') !== NULL) ? Lib::config($this->_module_name.'.downloadfile_upload_path') : array();
		
		$this->_upload_url	= (Lib::config($this->_module_name.'.downloadfile_upload_url') !== NULL) ? Lib::config($this->_module_name.'.downloadfile_upload_url') : array();

		$this->file				= new Model_DownloadFile;
		$this->downloadType		= new Model_DownloadType;
		
		// Category Download
		$this->category		= new Model_ProductCategory;
		// Product Download
		$this->product		= new Model_Product;		
		
        $this->now      	= time();	

		foreach ($downloadtypes as $row1) {
			$buffers[$row1->id]	= $row1;
		}
		$this->downloadTypes		= $buffers;
		
		$this->statuses		= array('publish','unpublish');
		
		//print_r($this->_prefs); exit();
		
		unset($downloadtype, $downloadtypes, $buffers, $buffers_release);
		
	}

	function action_index () {
		$downloadtype_id = $this->id1;
		
		if ($downloadtype_id != '')
			$this->_prefs	= array_merge($this->_prefs, (Lib::config('site.'.$this->_class_name.'_'.$downloadtype_id.'_index_fields') !== NULL) ? Lib::config('site.'.$this->_class_name.'_'.$downloadtype_id.'_index_fields') : array());

		$where_cond	= array('status !='	=> 'deleted');

		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if (isset($post['field']) || isset($post['keyword'])) {
				$post->rule('keyword', 'regex', array(':value', '/^[a-z_.\-]++$/iD'));

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

		$sort		= isset($params['id2']) ? $this->id2 : 'order';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[1];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN.$this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;


		$table_headers	= array('title'			=> 'Title',
								'description' 	=> 'Description',
								'type_id'	=> 'Download Type',
								'order'		=> 'Order',
								'count'		=> 'Hits',
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

		if ($downloadtype_id != '' && (($this->downloadType = $this->downloadType->load($downloadtype_id)) || ($this->downloadType =Model_DownloadType::instance()->load_by_name($downloadtype_id))))
			$downloadtype_id	= $this->downloadType->id;
		else
			$downloadtype_id	= '';

		if ($downloadtype_id != '' && is_array($where_cond))	
			$where_cond	= array_merge(array('type_id' => $downloadtype_id), $where_cond);

		$default_order	= array('type_id'=>'asc');
		$order_by		= array_merge($default_order,$order_by);
		$total_rows	= count($this->file->find($where_cond));
		$total_record 	= $total_rows;
		$listings	= $this->file->find($where_cond, $order_by, $per_page, $offset);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);

		/** Generate Thumbnails **/
		
		//Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);

		/** Views **/
		//print_r($this->file->last_order('',TRUE));
		//print_r($this->file);
		
		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'max_order'		=> $this->file,
									'min_order'		=> $this->file,
									'module_menu'	=> $this->_module_menu,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'type_id'		=> $downloadtype_id,
									'downloadtypes'	=> $this->downloadTypes,
									'total_record'	=> $total_record,			
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'downloads'		=> $this->downloadTypes,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime'),
									'mime_icon'		=> Lib::config($this->_module_name.'.mime_icon')
									);

		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_add () {
		$downloadtype_id		= $this->id1;
		
		$fields	= array('type_id'			=> '',
						'category_id'		=> '',
						'product_id'		=> '',	
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

		if ($downloadtype_id != '' && (($this->downloadType = Model_DownloadType::instance()->load($downloadtype_id)) || ($this->downloadType = Model_DownloadType::instance()->load_by_name($downloadtype_id))))
			$fields['type_id']	= $this->downloadType->id;

		if ($_POST) {
			if ($_FILES) 
				$post	= Validation::factory(array_merge($_POST,$_FILES));
			else
				$post	= Validation::factory($_POST);
			
			    $post->rule('title', 'not_empty');
                $post->rule('title', 'min_length', array(':value', 4));
                $post->rule('name', 'not_empty');
                $post->rule('name', 'min_length', array(':value', 4));
				$post->rule('type_id', 'not_empty');
				$post->rule('category_id', 'not_empty');
				$post->rule('product_id', 'not_empty');
                $post->rule('description', 'not_empty');
                $post->rule('description', 'min_length', array(':value', 4));
                $post->rule('status', 'not_empty');
                
                $post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));

			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					// Set if this is optional or not, return true
					if (isset($row_params['optional'])) {
						$post->rule($row_name, 'Upload::not_empty');
					}
					
					// Set if this is not the right file type, return true
					if (isset($row_params['file_type'])) {
						$post->rule($row_name, 'Upload::type', array(':value', explode(",", $row_params['file_type'])));
					} 

					// Set if this is not the right max file size, return true
					if (isset($row_params['max_file_size'])) {
						$post->rule($row_name, 'Upload::size', array(':value', $row_params['max_file_size']));
					} 
					
					// Set if this file has valid name in database, return true
					if (isset($post[$row_name]['name'])) {
						$post->rule($row_name, array($this, '_unique_filename'), array(':validation', $row_name));
					}
				}
			}
			
			if ($post->check()) {
				
				$fields	= $post->as_array();
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
			
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
						$params		= array(
										'type_id'		=> $fields['type_id'],
										'category_id'	=> $fields['category_id'],
										'product_id'	=> $fields['product_id'],
										'name'			=> $fields['name'],
										'title'			=> $fields['title'],
										'description'	=> $fields['description'],
										'field_name'	=> $row_name,
										'file_name'		=> $file_data['basename'],
										'file_type'		=> $file_mime,
										'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : NULL,
										'allow_comment'	=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
										'order'			=> !empty($fields['order']) ? $fields['order'] : $this->file->last_order($fields['type_id']) + 1,
										'user_id'		=> (isset($this->acl->user->id)) ? $this->acl->user->id : 0,
										'status'		=> $fields['status']);

						$id				= $this->file->add($params);

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
		
		$categories	= $this->category->find();
		$products	= $this->product->find();

		/** Views **/
		
		//print_r($errors);
		
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'downloads'		=> $this->downloadTypes,
									'categories'	=> $categories,
									'products'		=> $products,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name
									);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  

	}

	public function action_view () {
		$this->file->id	= $this->id1;

		if (!$this->file->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		if ($this->file->type_id != 0) {
			$downloadtype			= $this->downloadType->load($this->file->type_id);
			$downloadtype			= HTML::chars($downloadtype->subject, TRUE);
		} else {
			$downloadtype			= 'This file doesn\'t have download';
		}

		/** Generate Thumbnails **/

		
		/** Views **/

		$content_vars		= array('file'			=> $this->file,
									'downloadtype'	=> $downloadtype,
									'class_name'	=> $this->_class_name,
									'module_name'	=> $this->_module_name,
									'module_menu'	=> $this->_module_menu,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
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
		$id				= $this->id1;
		$this->file->id	= $id;

		if (!$this->file->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		
		$fields	= array('type_id'		=> '',
						'category_id'	=> '',
						'product_id'	=> '',		
						'name'			=> '',
						'title'			=> '',
						'description'	=> '',
						'caption'		=> '',
						'allow_comment'	=> (isset($this->_prefs['show_allow_comment']) && $this->_prefs['show_allow_comment']) ? '' : 0,
						'status'		=> '');

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

			$post->rule('title', 'not_empty');
            $post->rule('title', 'min_length', array(':value', 2));
            $post->rule('name', 'not_empty');
			$post->rule('name', 'min_length', array(':value', 2));
			$post->rule('type_id', 'not_empty');
			$post->rule('category_id', 'not_empty');			
			$post->rule('product_id', 'not_empty');						
			$post->rule('description', 'not_empty');
            $post->rule('description', 'min_length', array(':value', 2));
            $post->rule('status', 'not_empty');

			if (isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					// Set if this is not the right file type, return true
					if (isset($row_params['file_type'])) {
						$post->rule($row_name, 'Upload::type', array(':value', explode(",", $row_params['file_type'])));
					} 

					// Set if this is not the right max file size, return true
					if (isset($row_params['max_file_size'])) {
						$post->rule($row_name, 'Upload::size', array(':value', $row_params['max_file_size']));
					}
					
				}
			}

			if ($post->check() && !empty($this->_prefs['uploads'])) {
				$fields	= $post->as_array();

				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					
					$fields['order']	= ($fields['type_id'] != $fields['_type_id']) ? $this->file->last_order($fields['type_id']) + 1 : $this->file->order;
					
					if (!$post[$row_name]['name']) {
						$params			= array('type_id'		=> $fields['type_id'],							
												'category_id'	=> $fields['category_id'],
												'product_id'	=> $fields['product_id'],
												'name'			=> $fields['name'],
												'title'			=> $fields['title'],
												'description'	=> $fields['description'],
												'caption'		=> $fields[$row_name.'_caption'],
												'allow_comment'	=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
												'order'			=> !empty($fields['order']) ? $fields['order'] : '',
												'status'		=> $fields['status']);

						foreach ($params as $var => $val) {
							$this->file->$var	= $val;
						}

						$this->file->update($params);

					} else {
			
						//$file_name	= $this->file->file_name;
						//$file_data	= pathinfo($this->_upload_path.$this->file->file_name);
						//$file_mime	= $this->file->file_type;
						
						$file_data	= pathinfo($post[$row_name]['name']);
						//print_r($post[$row_name]);
						if ($this->_prefs['show_filename'] == TRUE) {
							// This will provide real filename
							$filename_upload	= preg_replace('/\.[^.]*$/', '', $file_data['basename']);
							// This will provide real filename based on Upload::save but with addition random char
							// $filename_upload	= NULL;
						}
						else {
							$filename_upload	= md5(time() + rand(100, 999));
						}
						
						if ($filename_upload == $this->file->file_name) {
							// Unlink physical file and delete in database
							@unlink($this->_upload_path.$this->file->file_name);
						}
						
						$file_name	= Lib::_upload_to($post[$row_name], $filename_upload.'.'.$file_data['extension'], $this->_upload_path, 0755);

						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];

						$params			= array('type_id'		=> $fields['type_id'],
												'category_id'	=> $fields['category_id'],
												'product_id'	=> $fields['product_id'],
												'name'			=> $fields['name'],
												'title'			=> $fields['title'],
												'description'	=> $fields['description'],
												'field_name'	=> $row_name,
												'file_name'		=> $file_data['basename'],
												'file_type'		=> $file_mime,
												'caption'		=> $fields[$row_name.'_caption'],
												'allow_comment'	=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
												'order'			=> !empty($fields['order']) ? $fields['order'] : '',
												'status'		=> $fields['status']);

						foreach ($params as $var => $val) {
							$this->file->$var	= $val;
						}

						$this->file->update($params);
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
			}
		} else {
			$fields	= array('type_id'			=> $this->file->type_id,
							'category_id'		=> $this->file->category_id,
							'product_id'		=> $this->file->product_id,							
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
		
		$categories	= $this->category->find();
		$products	= $this->product->find(array('category_id'=>$this->file->category_id));
		
		/** Views **/

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'file'			=> $this->file,
									'downloads'		=> $this->downloadTypes,
									'statuses'		=> $this->statuses,
									'categories'	=> $categories,
									'products'		=> $products,
									'module_name'	=> $this->_module_name,
									'module_menu'	=> $this->_module_menu,
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

	public function action_productlookup () {
		$id = Request::$current->post('id');
		if ($id == '') {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}
		
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
			
			// This setting is used for deleting all file included
			$products = Model_Product::instance()->find(array('category_id'=>$id,'status'=>'publish'));
			
			if (!empty($products)) {
				$return = array();
				$i=0;
				foreach ($products as $product) {
					$return['products'][$i]['id']		= $product->id;
					$return['products'][$i]['subject']	= $product->subject;
					$i++;
				}
				echo json_encode($return);
			} else {
				echo 0;
			}	
			
		} else {
			$this->redirect(ADMIN.$this->request->controller());
			//die();
		}

		exit();		
		
	}
	
	public function action_delete () {
		$this->file->id	= $this->id1;

		if (!$this->file->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}
		
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
			
			// This setting is used for deleting all file included
			$downloadfiles = Model_DownloadFile::instance()->load($this->id1);
			if (!empty($downloadfiles)) {
				if (is_readable($this->_upload_path.$downloadfiles->file_name)) {
					@unlink($this->_upload_path.$downloadfiles->file_name);
					Model_DownloadFile::instance()->delete($this->id1);
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
		
	}

	public function action_download() {
		/*
		$files		= $this->id1;
		
		$where_cond	= array('file_name'	=> $files);

		$files		= $this->file->find($where_cond);

		foreach ($files as $row) {
			Lib::_download_file_force('',$this->_upload_url.$row->file_name);
		}
		 * 
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
	
	/*** Function Access ***/
	
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
	
	// Action for ordering item list
	public function action_order() {
		if (empty($this->id2) && empty($this->id3))
			continue;
			
		if ($this->id1 == 'up' && $this->id2 && $this->id3 && $this->id4) {
			
			$where_cond = array('order'=>$this->id3 + 1, 'type_id'=>$this->id4);
			$order_by   = array('order'=>'asc');
			
			$order_default = $this->file->find($where_cond,$order_by,1);
			
			if (!empty($order_default)) {
				foreach ($order_default as $default) {
					$default->order  = ($default->order != $this->file->first_order($default->type_id)) ? $default->order - 1 : 0;
					$default->update();
				}
			}
			$this->file->id = $this->id2;
			$this->file->load();
			$this->file->order = $this->id3 + 1;
			$this->file->update();
		} 
		if ($this->id1 == 'down' && $this->id2 && $this->id3 && $this->id4) {
			
			$where_cond = array('order'=>$this->id3 - 1,'type_id'=>$this->id4);
			$order_by	= array('order'=>'asc');
			
			$order_default = $this->file->find($where_cond,$order_by,1);
			
			if (!empty($order_default)) {
				foreach ($order_default as $default) {
					
					$default->order  = ($default->order != $this->file->last_order($default->type_id)) ? $default->order + 1 : 1;
					
					$default->update();
				}
			}
			$this->file->id = $this->id2;
			$this->file->load();
			$this->file->order = $this->id3 - 1;
			$this->file->update();
		}
		
		if ($this->request->is_ajax()) {
			echo $this->id2;
			exit();
		}
		
		$this->redirect(ADMIN.$this->_class_name);		
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
		if (!isset($array[$field]))
			return;
		
		$name = Model_DownloadFile::instance()->find_count(array('name' => $array[$field]));
		
		if ($name)
			return $array->error($field, 'unique_name');
	}

	public function _valid_type_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;

		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->downloadType->find_count($where_cond) != 0);

		if (!$parent_exists)
			$array->add_error($field, 'invalid_type_id');
	}

	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->add_error($field, 'invalid_status');
	}

}
