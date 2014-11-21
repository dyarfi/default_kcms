<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_Product extends Controller_Backend_BaseAdmin {
	
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

	protected $product;
	protected $products;
	protected $category;
	protected $categories;
	protected $user;
	protected $users;
	protected $statuses;

	private $_parent_product;
	
	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_class_name	= $this->controller;	
		$this->_module_menu	= $this->acl->module_menu;
		
		$this->_search_keys	= array('name'		=> 'Name',
									'title'		=> 'Title',
									'status'	=> 'Status');

		$this->_prefilter_keys	= array('category_id');
		
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();

		$this->product		= new Model_Product;
		$this->category		= new Model_ProductCategory;
		$this->user			= new Model_User;

		$where_cond			= array('status'	=> 'publish');
		$this->products		= $this->product->find($where_cond);
		$this->categories	= $this->category->find($where_cond);

		$this->file			= new Model_ProductFile();
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

		/** Table sorting **/

		$params		= Request::$current->param();
			
		$sorts		= array('asc', 'desc');

		$sort		= isset($params['sort']) ? $params['sort'] : 'order';
		$order		= isset($params['order']) ? $params['order'] : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($params['page']) ? $params['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		$page_url	= '';
		$base_url	= ADMIN.$this->_class_name.'/index/page/';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array('title'		=> 'Title',
								/*'order'		=> 'Order',*/
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

		$order_by		= array('order'			=> $order);

		$total_rows	= $this->product->find_count($where_cond);
		$listings	= $this->product->find($where_cond, $order_by, $per_page, $offset);
		

		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);

			$this->redirect($base_url.$page_index);
			return;
		}
		
		$total_record	= $total_rows;
		
		$this->session->set($this->_class_name.'_index', $base_url.$page_index);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);

		/** Views **/

		$content_vars		= array('listings'		=> $listings,
									'total_record'	=> $total_record,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,	
									'search_keys'	=> $this->_search_keys,
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
$category_id = $this->id1;
		$fields	= array('category_id'		=> '',
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
		$fields['category_id']	= $category_id;
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
				$where_cond	= array('category_id'		=> $fields['category_id']);
				$last_order	= $this->product->find_count($where_cond) + 1;
				if (isset($fields['order']) && $fields['order'] < $last_order) {
					$where_cond	= array('category_id'	=> $fields['category_id'],
										'order >='		=> $fields['order']);
					$pages	= $this->product->find($where_cond);
					foreach ($pages as $row) {
						$row->order	= $row->order + 1;
						$row->update();
					}
					unset($pages);
				}
				$params	= array('category_id'		=> $fields['category_id'],
								'name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'description'		=> $fields['description'],
								'user_id'			=> (isset($this->acl->logged_user->id)) ? $this->acl->logged_user->id : 0,
								'status'			=> $fields['status']);
				$id		= $this->product->add($params);
				if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						//if (!upload::required($_FILES[$row_name]) || !upload::valid($_FILES[$row_name]))
							//continue;
						if (!Upload::not_empty($_FILES[$row_name]) || !Upload::type($_FILES[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($_FILES[$row_name]))
							continue;
						if (!File::exts_by_mime($post[$row_name]['type']))
							continue;
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($_FILES[$row_name]['name']);
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,'0777');
						$file_data	= pathinfo($file_name);
						$file_mime	= $_FILES[$row_name]['type'];
						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params			= array('product_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
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
		$order_by			= array('category_id'	=> 'ASC',
									'order'			=> 'ASC');
		$orders				= $this->product->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'categories'	=> $this->categories,
									'orders'		=> $orders,
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
		$category_id = $this->id2;
		$this->product->id	= $id;
		if (!$this->product->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		/** Views **/
		if ($this->product->category_id != 0) {
			$category		= $this->category->load($this->product->category_id);
			$category		= HTML::chars($category->title, TRUE);
		} else {
			$category		= 'This page doesn\'t have category';
		}
		if ($this->product->order == 0) {
			$order			= '-';
		} else if ($this->product->order != 1) {
			$where_cond		= array('category_id'	=> $this->product->category_id,
									'order'			=> ($this->product->order - 1));
			$pages		= $this->product->find($where_cond, '', 1);
			if (isset($pages[0]))
				$order		= 'After category '.HTML::chars($pages[0]->subject, TRUE);
			else
				$order		= '';
		} else {
			$order			= 'At the beginning';
		}
		$where_cond			= array('product_id'	=> $this->product->id);
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
		$content_vars		= array('product'		=> $this->product,
									'category'		=> $category,
									'files'			=> $files,
									'order'			=> $order,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									'category_id'	=> $category_id,
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
		$this->product->id	= $id;
		if (!$this->product->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array('category_id'		=> '',
						'name'				=> '',
						'subject'			=> '',
						'description'		=> '',
						'order'				=> '',
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
			// $post->add_callbacks('name', array($this, '_unique_name'));
//			/print_r($fields['category_id']); exit(
			//print_r($_POST); exit();
			$fields['category_id'] = (empty($fields['category_id'])) ? '0' : $fields['category_id'];
			if ($post->check()) {
				$fields	= $post->as_array();
				$order		= (@$fields['order'] == 1) ? @$fields['order'] : (@$fields['order'] - 1);
				$where_cond	= array('category_id'	=> $fields['category_id']);
				$last_order	= $this->product->find_count($where_cond) + 1;
				if ($this->product->category_id != $fields['category_id']) {
					$where_cond	= array('category_id'	=> $this->product->category_id,
										'order >='		=> $this->product->order);
					$orders		= $this->product->find($where_cond);
					foreach ($orders as $row) {
						$row->order	= $row->order - 1;
						$row->update();
					}
					$where_cond	= array('category_id'	=> $fields['category_id'],
										'order >='	=> $order);
					$orders		= $this->product->find($where_cond);
					foreach ($orders as $row) {
						$row->order	= $row->order + 1;
						$row->update();
					}
				} else {
					if (@$fields['order'] == 1) {
						$where_cond	= array('category_id'	=> $fields['category_id'],
											'order >='		=> $order,
											'order <='		=> $this->product->order,
											'status !='		=> 'deleted');
						$orders		= $this->product->find($where_cond);
						foreach ($orders as $row) {
							$row->order	= $row->order + 1;
							$row->update();
						}
					} else if ($this->product->order < @$fields['order']) {
						$where_cond	= array('category_id'	=> $fields['category_id'],
											'order >'		=> $this->product->order,
											'order <='		=> $order,
											'status !='		=> 'deleted');
						$orders		= $this->product->find($where_cond);
						foreach ($orders as $row) {
							$row->order	= $row->order - 1;
							$row->update();
						}
					} else if ($this->product->order > @$fields['order']) {
						$where_cond	= array('category_id'	=> $fields['category_id'],
											'order >'		=> $order,
											'order <='		=> $this->product->order,
											'status !='		=> 'deleted');
						$orders		= $this->product->find($where_cond);
						foreach ($orders as $row) {
							$row->order	= $row->order + 1;
							$row->update();
						}
						$order		= $order + 1;
					} else if ($this->product->order == @$fields['order']) {
						$order		= $order + 1;
					}
				}
				unset($orders);
				$params	= array('category_id'		=> $fields['category_id'],
								'name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'description'		=> $fields['description'],
								'order'				=> isset($fields['order']) ? $order : 0,
								'status'			=> $fields['status']);
				foreach ($params as $var => $val) {
					$this->product->$var	= $val;
				}
				$this->product->update();
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('product_id'	=> $this->product->id);
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
						// if (!isset($_FILES[$row_name]) || (isset($_FILES[$row_name]) && !upload::required($_FILES[$row_name]) || !upload::valid($_FILES[$row_name])))
							// continue;
						if (!Upload::not_empty($post[$row_name]) || !Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || !Upload::valid($post[$row_name]))
							continue;
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						$file_name	= Upload::save($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path,'0777');
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						if (!isset($files[$row_name])) {
							$params			= array('product_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							$this->file->add($params);
						} else {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$params			= array('product_id'	=> $id,
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
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->product->id.'/'.$category_id);
				else
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->product->id);
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
			$fields	= array('category_id'		=> $this->product->category_id,
							'name'				=> $this->product->name,
							'subject'			=> $this->product->subject,
							'description'		=> $this->product->description,
							'order'				=> $this->product->order,
							'status'			=> $this->product->status);
			$where_cond			= array('product_id'	=> $this->product->id);
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
		$where_cond			= array('status !='		=> 'deleted');
		$order_by			= array('category_id'	=> 'ASC',
									'order'			=> 'ASC');
		$orders				= $this->product->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,	
									'upload_url'	=> $this->_upload_url,	
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'product'			=> $this->product,
									'categories'	=> $this->categories,
									'files'			=> $files,
									'orders'		=> $orders,
									'statuses'		=> $this->statuses,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									'category_id'	=> $category_id);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content;
	}

	public function action_delete () {
		$this->product->id	= $this->id1;

		if (!$this->product->load()) {
			$this->redirect(ADMIN .'error/invalid_request');
			return;
		}

		//$this->product->status	= 'deleted';
		//$this->product->update();

		//$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';

		//$this->redirect($redirect_url);
		
		$this->product->status	= 'deleted';
		
		if($this->product->update())
			echo 1;
		else
			echo 0;
		
		exit;
	}
	
	// Action for update item status
	public function action_change() {
		
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			foreach ($rows as $row) {
				$this->product->id	= $row;

				if (!$this->product->load())
					continue;

				$this->product->status	= $this->request->post('select_action');
				$this->product->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN.$this->_class_name);
			
		}
		
	}
	
	/** CALLBACKS **/

	public function _unique_name (Validation $array, $field) {
		if (isset($this->product->name) && $this->product->name == $array[$field])
			return;

		$where_cond		= array('name'	=> $array[$field]);
		$name_exists	= ($this->product->find_count($where_cond) != 0);

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
