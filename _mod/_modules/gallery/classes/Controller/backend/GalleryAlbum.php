<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_GalleryAlbum extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

	protected $album;
	protected $albums;
	protected $user;
	protected $users;
	protected $statuses;

	private $_parent_album;

	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_module_name	= 'gallery';
		$this->_class_name	= $this->controller;	
		$this->_module_menu	= $this->acl->module_menu;
		
		$this->_search_keys	= array('name'		=> 'Name',
									'subject'	=> 'Subject',
									'status'	=> 'Status');
		
		$this->_prefs		= (Lib::config($this->_class_name.'.'.$this->_class_name) !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name) : array();
		
		$this->_prefs		= array_merge($this->_prefs, (Lib::config('site.'.$this->_class_name.'_fields') !== NULL) ? Lib::config('site.'.$this->_class_name.'_fields') : array());

		$this->album		= new Model_GalleryAlbum;
		$this->user			= new Model_User;

		$where_cond			= array('status'	=> 'publish');
		$this->albums		= $this->album->find($where_cond);

		$where_cond			= array('status'	=> 'active');
		$this->users		= $this->user->find($where_cond);

		$this->statuses		= array('publish',
									'unpublish');
	}

	function action_index () {
		
		$parent_id = $this->id1;
		
		if ($parent_id != '')
			$this->_prefs	= array_merge($this->_prefs, (Lib::config('site.'.$this->_module_name.'_'.$parent_id.'_index_fields') !== NULL) ? Lib::config('site.'.$this->_module_name.'_'.$parent_id.'_index_fields') : array());

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

		$where_cond	= isset($where_cond) ? $where_cond : '';
		
		if ($parent_id != '' && (($this->_parent_album = $this->album->load($parent_id)) || ($this->_parent_album = $this->album->load_by_name($parent_id)))) {
			$current_where	= is_array($where_cond) ? $where_cond : array();

			$where_cond		= array('parent_id'		=> $this->_parent_album->id,
									'status !='		=> 'deleted');
			$order_by		= array('order'			=> $order);

			$listings		= $this->album->find(array_merge($where_cond, $current_where), $order_by);

			$buffers		= array();
			
			foreach ($listings as $row) {
				$buffers[] = $row;

				do {
					$where_cond = array('parent_id'	=> $row->id,
										'status !='	=> 'deleted');

					$subs		= $this->album->find(array_merge($where_cond, $current_where), $order_by);

					if (count($subs) != 0)
						$buffers = array_merge($buffers, $subs);
					else
						break;
				} while (0);
			}

			foreach ($buffers as $index => $row) {
				$row->sub_level = $row->sub_level - ($this->_parent_album->sub_level + 1);

				$buffers[$index] = $row;
			}

			$total_rows	= count($buffers);
			$listings	= array_slice($buffers, intval($offset), intval($per_page));
		}

		if (!isset($total_rows) || !isset($listings)) {
			$total_rows	= $this->album->find_count($where_cond);
			$listings	= $this->album->find($where_cond, $order_by, $per_page, $offset);
		}
		
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
									'module_name'	=> $this->_module_name,	
									'search_keys'	=> $this->_search_keys,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'parent_id'		=> $parent_id);

		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_add () {
		$parent_id = $this->id1;
		$fields	= array('parent_id'			=> '',
						'name'				=> '',
						'subject'			=> '',
						'description'		=> '',
						'user_id'			=> '',
						'order'				=> '',
						'status'			=> '');

		$errors	= $fields;

		$fields['parent_id']	= $parent_id;

		if ($_POST) {
			$post	= new Validation($_POST);

			$post->rule('subject', 'not_empty');
			$post->rule('subject', 'min_length', array(':value', 4));
			$post->rule('name', 'not_empty');
			$post->rule('name', 'min_length', array(':value', 4));			
			$post->rule('status', 'not_empty');

			$post->rule('parent_id', array($this, '_valid_parent_id'), array(':validation', 'parent_id'));
			$post->rule('user_id', array($this, '_valid_user_id'), array(':validation', 'user_id'));
			$post->rule('status', array($this, '_valid_status'), array(':validation', 'status'));
			$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
			
			if ($post->check()) {
				$fields	= $post->as_array();

				if ($fields['parent_id'] != 0) {
					$parent		= $this->album->load($fields['parent_id']);
					$sub_level	= $parent->sub_level + 1;

					unset($parent);
				} else {
					$sub_level	= 0;
				}

				$where_cond	= array('parent_id'	=> $fields['parent_id']);
				$last_order	= $this->album->find_count($where_cond) + 1;

				if (isset($fields['order']) && $fields['order'] < $last_order) {
					$where_cond	= array('parent_id'	=> $fields['parent_id'],
										'order >'	=> $fields['order']);
					$albums	= $this->album->find($where_cond);

					foreach ($albums as $row) {
						$row->order	= $row->order + 1;
						$row->update();
					}
				}

				$params	= array('parent_id'		=> $fields['parent_id'],
								'name'			=> $fields['name'],
								'subject'		=> $fields['subject'],
								'description'	=> !empty($fields['description']) ? $fields['description'] : '',
								'user_id'		=> !empty($fields['user_id']) ? $this->acl->user->id : 1,
								'sub_level'		=> $sub_level,
								'order'			=> @$fields['order'],
								'status'		=> $fields['status']);

				$id		= $this->album->add($params);

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
		
		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'statuses'	=> $this->statuses,
									'albums'	=> $this->albums,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  
		
	}

	public function action_view () {
		$this->album->id	= $this->id1;

		if (!$this->album->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		/** Views **/

		if ($this->album->parent_id != 0) {
			$parent			= $this->album->load($this->album->parent_id);
			$parent			= HTML::chars($parent->subject, TRUE);
		} else {
			$parent			= 'This album is parent';
		}

		if ($this->album->user_id != 0) {
			$user			= $this->user->load($this->album->user_id);
			$user			= HTML::chars($user->name.' ('.$user->email.')', TRUE);
		} else {
			$user			= 'System';
		}

		if ($this->album->order != 1) {
			$where_cond		= array('parent_id'	=> $this->album->parent_id,
									'order'		=> ($this->album->order - 1));
			$albums			= $this->album->find($where_cond, '', 1);
			$order			= 'After album '.HTML::chars($this->albums[0]->subject, TRUE);
		} else {
			$order			= 'At the beginning';
		}

		$content_vars		= array('album'		=> $this->album,
									'parent'	=> $parent,
									'user'		=> $user,
									'order'		=> $order,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory('gallery/backend/'.$this->_class_name.'_view');
		
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_edit () {
		$id					= $this->id1;
		$this->album->id	= $id;

		if (!$this->album->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}

		$fields	= array('parent_id'			=> '',
						'name'				=> '',
						'subject'			=> '',
						'description'		=> '',
						'user_id'			=> '',
						'order'				=> '',
						'status'			=> '');

		$errors	= $fields;

		if ($_POST) {
			$post	= new Validation($_POST);

			$post->rule('subject', 'not_empty');
			$post->rule('subject', 'min_length', array(':value', 4));
			$post->rule('name', 'not_empty');
			$post->rule('name', 'min_length', array(':value', 4));			
			$post->rule('status', 'not_empty');

			$post->rule('parent_id', array($this, '_valid_parent_id'), array(':validation', 'parent_id'));
			$post->rule('user_id', array($this, '_valid_user_id'), array(':validation', 'user_id'));
			$post->rule('status', array($this, '_valid_status'), array(':validation', 'status'));
			$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));

			if ($post->check()) {
				$fields	= $post->as_array();

				if ($fields['parent_id'] != 0) {
					$parent		= $this->album->load($fields['parent_id']);
					$sub_level	= $parent->sub_level + 1;

					unset($parent);
				} else {
					$sub_level	= 0;
				}

				$order		= ($fields['order'] == 1) ? $fields['order'] : ($fields['order'] - 1);

				$where_cond	= array('parent_id'	=> $fields['parent_id']);
				$last_order	= $this->album->find_count($where_cond) + 1;

				if ($this->album->sub_level != $sub_level) {
					$where_cond	= array('parent_id'	=> $this->album->parent_id,
										'sub_level'	=> $this->album->sub_level,
										'order >='	=> $this->album->order);
					$orders		= $this->album->find($where_cond);

					foreach ($orders as $row) {
						$row->order	= $row->order - 1;
						$row->update();
					}

					$where_cond	= array('parent_id'	=> $fields['parent_id'],
										'sub_level'	=> $sub_level,
										'order >='	=> $order);
					$orders		= $this->album->find($where_cond);

					foreach ($orders as $row) {
						$row->order	= $row->order + 1;
						$row->update();
					}
				} else {
					if ($this->album->order < $fields['order']) {
						$where_cond	= array('parent_id'	=> $fields['parent_id'],
											'sub_level'	=> $sub_level,
											'order >'	=> $this->album->order,
											'order <='	=> $order);
						$orders		= $this->album->find($where_cond);

						foreach ($orders as $row) {
							$row->order	= $row->order - 1;
							$row->update();
						}
					} else if ($this->album->order > $fields['order']) {
						$where_cond	= array('parent_id'	=> $fields['parent_id'],
											'sub_level'	=> $sub_level,
											'order >='	=> $order,
											'order <'	=> $this->album->order);
						$orders		= $this->album->find($where_cond);

						foreach ($orders as $row) {
							$row->order	= $row->order + 1;
							$row->update();
						}
					}
				}

				$this->album->parent_id		= $fields['parent_id'];
				$this->album->name			= $fields['name'];
				$this->album->subject		= $fields['subject'];
				$this->album->description	= !empty($fields['description']) ? $fields['description'] : '';
				$this->album->user_id		= !empty($fields['user_id']) ? $fields['user_id'] : '';
				$this->album->sub_level		= $sub_level;
				$this->album->order			= $order;
				$this->album->status		= $fields['status'];

				$this->album->update();

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
			$fields	= array('parent_id'			=> $this->album->parent_id,
							'name'				=> $this->album->name,
							'subject'			=> $this->album->subject,
							'description'		=> $this->album->description,
							'user_id'			=> $this->album->user_id,
							'order'				=> $this->album->order,
							'status'			=> $this->album->status);
		}
		
		/** Views **/

		$where_cond			= array('parent_id'		=> $this->album->parent_id);
		$orders				= $this->album->find($where_cond);

		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'album'			=> $this->album,
									'albums'		=> $this->albums,
									'users'			=> $this->users,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'orders'		=> $orders,
									'statuses'		=> $this->statuses);

		$content_vars		= array_merge($content_vars, $this->_prefs);

		$content			= View::factory('gallery/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_delete () {
		$this->album->id	= $this->id1;

		if (!$this->album->load()) {
			$this->redirect(ADMIN .'error/invalid_request');
			return;
		}

		//$this->album->status	= 'deleted';
		//$this->album->update();

		//$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';

		//$this->redirect($redirect_url);
		
		$this->album->status	= 'deleted';
		
		if($this->album->update())
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
				$this->album->id	= $row;

				if (!$this->album->load())
					continue;

				$this->album->status	= $this->request->post('select_action');
				$this->album->update();
			}

			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN.$this->_class_name);
			
		}
		
	}
	
	/** CALLBACKS **/

	public function _unique_name (Validation $array, $field) {
		if (isset($this->album->name) && $this->album->name == $array[$field])
			return;

		$where_cond		= array('name'	=> $array[$field]);
		$name_exists	= ($this->album->find_count($where_cond) != 0);

		if ($name_exists)
			$array->error($field, 'name_exists');
	}

	public function _valid_parent_id (Validation $array, $field) {
		if ($array[$field] == 0)
			return TRUE;

		$where_cond		= array('id'	=> $array[$field]);
		$parent_exists	= ($this->album->find_count($where_cond) != 0);

		if (!$parent_exists)
			$array->error($field, 'invalid_parent_id');
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
}
