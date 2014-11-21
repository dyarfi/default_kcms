<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_ArticleCategory extends Controller_Backend_BaseAdmin {
	
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
	protected $_upload_path;
	protected $_upload_url;
	
	protected $category;
	protected $categories;
	protected $statuses;
	
	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_module_name		= 'article';
		
		$this->_class_name		= $this->controller;
		
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_search_keys		= array(/*'name'		=> 'Name',*/
										'title'		=> 'Title',
										'name'		=> 'Name',
										'status'	=> 'Status');
		$this->_prefs			= (Lib::config($this->_module_name.'.article_category_fields') !== NULL) ? Lib::config($this->_module_name.'.article_category_fields') : array();
		$this->_upload_path		= (Lib::config($this->_module_name.'.category_upload_path') !== NULL) ? Lib::config($this->_module_name.'.category_upload_path') : array();
		$this->_upload_url		= (Lib::config($this->_module_name.'.category_upload_url') !== NULL) ? Lib::config($this->_module_name.'.category_upload_url') : array();
		$this->category			= new Model_ArticleCategory;
		$this->file				= new Model_ArticleCategoryFile;
		$where_cond				= array('status !='	=> 'deleted');
		$this->categories		= $this->category->find($where_cond);
		$this->position			= array(1	=> 'Top',
										2	=> 'Bottom',
										3	=> 'Top & Bottom');
		$this->statuses			= array('publish',
										'unpublish');		
		unset($category);
	}
	function action_index () {
		$where_cond	= array('status !='	=> 'deleted');
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
		/** Table sorting **/
		$params		= Request::$current->param();
		$sorts		= array('asc', 'desc');
		$sort		= isset($params['id2']) ? $this->id2 : 'title';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);
		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		//$page_index	= isset($params['page']) ? $params['no'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 4;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN.$this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;
		$table_headers	= array('title'		=> 'Title',
								/*'order'	=> 'Order',*/
								/*'position'	=> 'Position',*/
								'status'	=> 'Status',
								'added'		=> 'Added',
								'modified'	=> 'Modified');
		if (!isset($this->_prefs['show_position']) && !$this->_prefs['show_position']) {
			unset($table_headers['position']);
		}
		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);
			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];
			$order_by	= array($sort	=> $order);
			$base_url	= ADMIN.$this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/article/';
		}
		/** Execute list query **/
		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';
		$where_cond	= isset($where_cond) ? $where_cond : '';
		//$total_rows	= count($this->category->find($where_cond));
		//print_r($this->_prefs); exit();
		$total_rows		= count($this->category->find($where_cond));
		$total_record 	= $total_rows;
		$listings	= $this->category->find($where_cond, $order_by, $per_page, $offset);
		/** Initialize pagination **/
		$pagination = Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 )
		);
		/** Generate Thumbnails **/
		//Lib::_auto_image_manipulation($this->_class_name, $this->file, $this->_prefs);
		/** Views **/
		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'position'		=> $this->position,
									'module_menu'	=> $this->_module_menu,	
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'search_keys'	=> $this->_search_keys,
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
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		//$this->template->article_title		= Lib::config('user.title');
		$this->template->content		= $content;
	}
	public function action_add () {
		$fields	= array('parent_id'			=> '',
						'name'				=> '',
						'title'				=> '',
						'description'		=> '',
						///'position'			=> '',
						'order'				=> '',
						'status'			=> '');
		if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
			foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				$fields[$row_name]	= '';
			}
		}
		$errors	= $fields;
		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);
				$post->rule('title', 'not_empty');
				$post->rule('title', 'min_length', array(':value', 4));
				$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
				//$post->rule('description', 'not_empty');
				//$post->rule('position', 'not_empty');
				$post->rule('order', 'not_empty');
				$post->rule('status', 'not_empty');
			if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['category_uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional']) {
						//$post->add_rules($row_name, 'upload::required');
						//$post->add_rules($row_name, 'upload::valid');
					//}
					//if (isset($row_params['file_type']))
						//$post->add_rules($row_name, 'upload::type['.$row_params['file_type'].']');
					//if (isset($row_params['max_file_size']))
						//$post->add_rules($row_name, 'upload::size['.$row_params['max_file_size'].']');
					if (Upload::type($post[$row_name],explode(',',$row_params['file_type'])) !== 1)
						continue;
				}
			}
			if ($post->check()) {
				$fields	= $post->as_array();
				if ($fields['parent_id'] != 0) {
					$parent		= $this->category->load($fields['parent_id']);
					$sub_level	= $parent->sub_level + 1;
					unset($parent);
				} else {
					$sub_level	= 0;
				}
				if(isset($fields['order'])) {
					$where_cond	= array('parent_id'	=> $fields['parent_id']);
					$last_order	= $this->category->find_count($where_cond) + 1;
					if ($fields['order'] < $last_order) {
						$where_cond	= array('parent_id'	=> $fields['parent_id'],
											'order >='	=> $fields['order'],
											'status !='	=> 'deleted');
						$categories	= $this->category->find($where_cond);
						foreach ($categories as $row) {
							$row->order	= $row->order + 1;
							$row->update();
						}
						unset($categories);
					}
				}
				$file_names	= '';
				$params	= array('parent_id'		=> $fields['parent_id'],
								'name'			=> $fields['name'],
								'title'			=> $fields['title'],
								'description'	=> $fields['description'],
								'file_name'		=> (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) ? $file_names : '',
								//'position'		=> (isset($this->_pref['show_position']) && $this->_prefs['show_position']) ? $fields['position'] : '1',
								'sub_level'		=> $sub_level,
								'order'			=> (isset($fields['order'])) ? $fields['order'] : 0,
								'user_id'		=> (isset($this->acl->user->id)) ? $this->acl->user->id : 0,
								'status'		=> $fields['status']);
				$id		= $this->category->add($params);
				if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						if (isset($fields['delete_'.$row_name]) && $fields['delete_'.$row_name] == 1 && isset($files[$row_name])) {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$this->file->delete();
						}
						if (!Upload::not_empty($post[$row_name]) || 
								!Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || 
								!Upload::valid($post[$row_name])) {
							continue;
						}
						//if (!File::exts_by_mime($post[$row_name]['type']))
							//continue;
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);	
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);			
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params = array('category_id'	 => $id,
											'field_name' => $row_name,
											'file_name'	 => $file_data['basename'],
											'file_type'	 => $file_mime,
											'caption'	 => isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							$this->file->add($params);
						}
					}
				}
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
		$where_cond			= array('status !='		=> 'deleted');
		$order_by			= array('order'			=> 'ASC');
		$orders				= $this->category->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'orders'		=> $orders,
									'class_name' 	=> $this->_class_name,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,	
									'categories'	=> $this->categories,
									'position'		=> $this->position,
									'statuses'		=> $this->statuses);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content;  
	}
	public function action_view () {
		$id = $this->id1;
		$this->category->id	= $id;
		if (!$this->category->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		//$this->category_file = array($this->category->file_name);
		//print_r($this->category); exit();
		/** Views **/
		if ($this->category->parent_id != 0) {
			$parent			= $this->category->load($this->category->parent_id);
			$parent			= HTML::chars($parent->title, TRUE);
		} else {
			$parent			= 'This category is parent';
		}
//		$order				= '';
		if ($this->category->order != 1) {
			$where_cond		= array('parent_id'	=> $this->category->parent_id,
									'order'		=> ($this->category->order - 1));
			$categories		= $this->category->find($where_cond, '', 1);
			$order			= 'After '.strtolower(str_replace('_', ' ', $this->_class_name)).' '.HTML::chars(@$categories[0]->title, TRUE);
		} else {
			$order			= 'At the beginning';
		}
		$where_cond			= array('category_id'	=> $id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();
		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}
		$files				= $buffers;
		unset($buffers);
		
		/** Generate Thumbnails **/
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		
		$content_vars		= array('category'	=> $this->category,
									'position'	=> $this->position,
									'parent'	=> $parent,
									'order'		=> $order,
									'files'		=> $files,
									'class_name' => $this->_class_name,
									'module_menu'	=> $this->_module_menu,			
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime')
									);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_view');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}
	public function action_edit () {
		$id = $this->id1;
		$this->category->id	= $id;
		if (!$this->category->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array('parent_id'			=> '',
						'name'				=> '',
						'title'				=> '',
						'description'		=> '',
						//'position'			=> '',
						'order'				=> '',
						'status'			=> '');
		if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
			foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				$fields[$row_name]	= '';
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
			//$post->rule('description', 'not_empty');
			if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['file_type']))
						//$post->add_rules($row_name, 'upload::type['.$row_params['file_type'].']');
					//if (isset($row_params['max_file_size']))
						//$post->add_rules($row_name, 'upload::size['.$row_params['max_file_size'].']');
					if (Upload::type($post[$row_name],explode(',',$row_params['file_type'])) !== 1)
						continue;	
				}
			}
			if ($post->check()) {
				$fields	= $post->as_array();
				if ($fields['parent_id'] != 0) {
					$parent		= $this->category->load($fields['parent_id']);
					$sub_level	= $parent->sub_level + 1;
					unset($parent);
				} else {
					$sub_level	= 0;
				}
				$order			= 0;
				if(isset($fields['order'])) {
					$order		= ($fields['order'] == 1) ? $fields['order'] : ($fields['order'] - 1);
					$where_cond	= array('parent_id'	=> $fields['parent_id']);
					$last_order	= $this->category->find_count($where_cond) + 1;
					if ($this->category->sub_level != $sub_level) {
						$where_cond	= array('parent_id'	=> $this->category->parent_id,
											'sub_level'	=> $this->category->sub_level,
											'order >'	=> $this->category->order,
											'status !='	=> 'deleted');
						$orders		= $this->category->find($where_cond);
						foreach ($orders as $row) {
							$row->order	= $row->order - 1;
							$row->update();
						}
						$where_cond	= array('parent_id'	=> $fields['parent_id'],
											'sub_level'	=> $sub_level,
											'order >'	=> $fields['order'],
											'status !='	=> 'deleted');
						$orders		= $this->category->find($where_cond);
						foreach ($orders as $row) {
							$row->order	= $row->order + 1;
							$row->update();
						}
					} else {
						if ($fields['order'] == 1) {
							$where_cond	= array('parent_id'	=> $fields['parent_id'],
												'sub_level'	=> $sub_level,
												'order >='	=> $order,
												'order <='	=> $this->category->order,
												'status !='	=> 'deleted');
							$orders		= $this->category->find($where_cond);
							foreach ($orders as $row) {
								$row->order	= $row->order + 1;
								$row->update();
							}
						} else if ($this->category->order < $fields['order']) {
							$where_cond	= array('parent_id'	=> $fields['parent_id'],
												'sub_level'	=> $sub_level,
												'order >'	=> $this->category->order,
												'order <='	=> $order,
												'status !='	=> 'deleted');
							$orders		= $this->category->find($where_cond);
							foreach ($orders as $row) {
								$row->order	= $row->order - 1;
								$row->update();
							}
						} else if ($this->category->order > $fields['order']) {
							$where_cond	= array('parent_id'	=> $fields['parent_id'],
												'sub_level'	=> $sub_level,
												'order >'	=> $order,
												'order <='	=> $this->category->order,
												'status !='	=> 'deleted');
							$orders		= $this->category->find($where_cond);
							foreach ($orders as $row) {
								$row->order	= $row->order + 1;
								$row->update();
							}
							$order		= $order + 1;
						} else if ($this->category->order == $fields['order']) {
							$order		= $order + 1;
						}
					}
					unset($orders);
				}
				$file_names	= '';
				if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('category_id'	=> $this->category->id);
					$files				= $this->file->find($where_cond);
					$buffers			= array();
					foreach ($files as $row) {
						$buffers[$row->field_name]	= $row;
					}
					$files				= $buffers;
					unset($buffers);
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						if (isset($fields['delete_'.$row_name]) && $fields['delete_'.$row_name] == 1 && isset($files[$row_name])) {
							//print_r($files[$row_name]->id); exit();
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
						//if (!isset($_FILES[$row_name]) || (isset($_FILES[$row_name]) && !upload::required($_FILES[$row_name]) || !upload::valid($_FILES[$row_name])))
							//continue;
						if (!File::exts_by_mime($post[$row_name]['type']))
							continue;
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						if($file_name != '') {
							$file_names	= $file_data['basename'];
						}
						if (!isset($files[$row_name])) {
							$params			= array('category_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							$this->file->add($params);
						} else {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$params			= array('category_id'		=> $id,
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
				$params	= array('parent_id'		=> $fields['parent_id'],
								'name'			=> $fields['name'],
								'title'			=> $fields['title'],
								'description'	=> $fields['description'],
								'file_name'		=> (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) ? $file_names : '',
								//'position'		=> (isset($this->_prefs['show_position']) && $this->_prefs['show_position']) ? $fields['position'] : '',
								'sub_level'		=> $sub_level,
								'order'			=> (isset($fields['order'])) ? $order : 0,
								'status'		=> $fields['status']);
				if($file_names == '')
					unset($params['file_name']);
				foreach ($params as $var => $val) {
					$this->category->$var	= $val;
				}
				$this->category->update();
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
		} else {
			$fields	= array('parent_id'			=> $this->category->parent_id,
							'name'				=> $this->category->name,
							'title'				=> $this->category->title,
							'description'		=> $this->category->description,
							'file_name'			=> $this->category->file_name,
							//'position'			=> $this->category->position,
							'order'				=> $this->category->order,
							'status'			=> $this->category->status);
			$where_cond			= array('category_id'	=> $this->category->id);
			$files				= $this->file->find($where_cond);
			$buffers			= array();
			foreach ($files as $row) {
				$buffers[$row->field_name]	= $row;
			}
			$files				= $buffers;
			unset($buffers);
			if (isset($this->_prefs['show_category_upload']) && $this->_prefs['show_category_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					$fields[$row_name]	= '';
					if (isset($row_params['caption']) && $row_params['caption'])
						$fields[$row_name.'_caption'] = (isset($files[$row_name])) ? $files[$row_name]->caption : '';
				}
			}
		}
		$where_cond			= array('category_id'	=> $id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();
		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}
		$files				= $buffers;
		unset($buffers);
		/** Views **/
		$where_cond			= array('parent_id'		=> $this->category->parent_id);
		$orders				= $this->category->find($where_cond);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'category'		=> $this->category,
									'categories'	=> $this->categories,
									'orders'		=> $orders,
									//'position'		=> $this->position,
									'statuses'		=> $this->statuses,
									'files'			=> $files,
									'module_menu'	=> $this->_module_menu,		
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'upload_url'	=> $this->_upload_url,
									'upload_path'	=> $this->_upload_path,
									'readable_mime'	=> Lib::config($this->_module_name.'.readable_mime')
									);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}
	public function action_delete ($id = '') {
		$this->category->id	= $id;
		if (!$this->category->load()) {
			$this->redirect(ADMIN.'error/invalid_request');
			return;
		}
		$this->category->status	= 'deleted';
		if($this->category->update())
			echo 1;
		else
			echo 0;
		exit;
//		$redirect_url	= ($this->acl->previous_url != '') ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
//
//		$this->redirect($redirect_url);
	}
	public function action_download ($file_name = '') {
		$where_cond	= array('file_name'	=> $file_name);
		$files		= $this->category->find($where_cond);
		foreach ($files as $row) {
			download::force(Lib::config($this->_class_name.'.category_upload_url').$row->file_name);
		}
	}
	private function _auto_image_manipulation () {
		$files		= $this->category->find();
		$buffers	= array('index.html');
		foreach ($files as $row) {
			/** store to save list **/
			if(file_exists(Lib::config($this->_class_name.'.category_upload_path').$row->file_name)) {
				if($row->file_name != '') {	
					$buffers[]		= $row->file_name;
					if (isset($this->_prefs['category_uploads']['image_1']['image_manipulation'])) {
						$params			= $this->_prefs['category_uploads']['image_1']['image_manipulation'];
						$image_ratio	= isset($params['ratio']) ? $params['ratio'] : 'auto';
						$thumbs			= isset($params['thumbnails']) ? $params['thumbnails'] : array();
						$crops			= isset($params['crop']) ? $params['crop'] : array();
						switch ($image_ratio) {
							case 'none':
								$image_ratio	= Image::NONE;
								break;
							case 'width':
								$image_ratio	= Image::WIDTH;
								break;
							case 'height':
								$image_ratio	= Image::HEIGHT;
								break;
							default:
								$image_ratio	= Image::AUTO;
						}
						$file_name		= Lib::config($this->_class_name.'.category_upload_path').$row->file_name;
						$file_data		= pathinfo($file_name);
						$image			= new Image($file_name);
						if (isset($params['thumbnails'])) {
							foreach ($params['thumbnails'] as $size) {
								if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'])) {
									/** store to save list **/
									$buffers[]		= $file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'];
									continue;
								}
								list($width, $height)	= explode('x', $size);
								$image->resize($width, $height, $image_ratio);
								$image->save($file_data['dirname'].'/'.$file_data['filename'].'_resize_'.$size.'.'.$file_data['extension']);
								/** store to save list **/
								$buffers[]		= $file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'];
							}
						}
						if (isset($params['crop'], $params['crop'][0])) {
							if (!is_array($params['crop'][0])) {
								if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'])) {
									/** store to save list **/
									$buffers[]		= $file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'];
									continue;
								}
								list($orig_w, $orig_h)	= getimagesize($file_name);
								$crop_aligns			= array('top', 'bottom', 'left', 'right', 'center');
								if (isset($params['crop'][0]) && strpos($params['crop'][0], 'x') != 0) {
									list($width, $height)	= explode('x', $params['crop'][0]);
									if (($orig_w / $width) < ($orig_h / $height))
										$image_ratio		= Image::WIDTH;
									else
										$image_ratio		= Image::HEIGHT;
								}
								if (isset($params['crop'][1]) && strpos($params['crop'][1], 'x') != 0)
									list($crop_x, $crop_y)	= explode('x', $params['crop'][1]);
								else if (isset($params['crop'][1]) && $params['crop'][1] == 'center')
									list($crop_x, $crop_y)	= array('center', 'center');
								else if (isset($params['crop'][1], $params['crop'][2]))
									list($crop_x, $crop_y)	= array($params['crop'][1], $params['crop'][2]);
								$image->resize($width, $height, $image_ratio);
								$image->crop($width, $height, $crop_x, $crop_y);
								$image->save($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension']);
								/** store to save list **/
								$buffers[]		= $file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'];
							} else {
								foreach ($params['crop'][0] as $crop_row) {
									if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'])) {
										/** store to save list **/
										$buffers[]		= $file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'];
										continue;
									}
									list($orig_w, $orig_h)	= getimagesize($file_name);
									$crop_aligns			= array('top', 'bottom', 'left', 'right', 'center');
									if (isset($params['crop'][0]) && strpos($crop_row, 'x') != 0) {
										list($width, $height)	= explode('x', $crop_row);
										if (($orig_w / $width) < ($orig_h / $height))
											$image_ratio		= Image::WIDTH;
										else
											$image_ratio		= Image::HEIGHT;
									}
									if (isset($params['crop'][1]) && strpos($params['crop'][1], 'x') != 0)
										list($crop_x, $crop_y)	= explode('x', $params['crop'][1]);
									else if (isset($params['crop'][1]) && $params['crop'][1] == 'center')
										list($crop_x, $crop_y)	= array('center', 'center');
									else if (isset($params['crop'][1], $params['crop'][2]))
										list($crop_x, $crop_y)	= array($params['crop'][1], $params['crop'][2]);

		

									$image->resize($width, $height, $image_ratio);

									$image->crop($width, $height, $crop_x, $crop_y);

		

									$image->save($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension']);

									/** store to save list **/

									$buffers[]		= $file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'];

								}

							}

						}
						
						unset($image);

					}

				}

			}		

		}
		
		$dir_name		= Lib::config($this->_class_name.'.category_upload_path');

		if(!is_dir($dir_name)) {
			mkdir($dir_name);
		}

		$folder_files	= scandir($dir_name);
		$deleted_files	= array_diff($folder_files, $buffers);
		foreach ($deleted_files as $row) {
			if(is_file($dir_name.$row) && is_writeable($dir_name.$row))
				unlink($dir_name.$row);
		}
	}
	
	/*** Function Access ***/

	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				// Change parent status

				$this->category->id	= $row;
				if (!$this->category->load())
					continue;

				$this->category->status	= $this->request->post('select_action');
				$this->category->update();

				// Change child status

				$children = $this->category->find(array('parent_id'=>$this->category->id));
				if (!empty($children)) {
					foreach ($children as $child) {
						$child->status = $this->request->post('select_action');
						$child->update();
					}
				}
//				/exit();			

			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);

		} else {	
			$this->redirect(ADMIN.$this->_class_name);			

		}

	}
	
	/** CALLBACKS **/

    public function _unique_name (Validation $array, $field) {
		if (!isset($array[$field]))
			return;

		$name = Model_ArticleCategory::instance()->find_count(array('name' => $array[$field],'status !='=>'deleted'));

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
}

