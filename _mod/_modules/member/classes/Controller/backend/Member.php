<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_Member extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
	protected $_upload_path;
	protected $_upload_url;
	
	protected $member;
	protected $user;
	protected $users;
	protected $settings;
	protected $level;
	protected $_genders;
	
	public function before () {
		// Get parent before method
        parent::before();
        
		$this->_class_name		= Request::$current->controller();
		$this->_class_name		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;

		$this->_search_keys 	= array('email'		=> 'E-mail',
										'name'		=> 'Name',
										'country'	=> 'Country',
										'state'		=> 'State/Province',
										'city'		=> 'City',
										'phone'		=> 'Phone',);

		$this->_prefilter_keys	= array('country',
										'state',
										'city',
										'phone');

		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();

		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();
		
		$this->_genders			= (Lib::config($this->_class_name.'.genders') !== NULL) ? Lib::config($this->_class_name.'.genders') : array();
		
		$this->statuses			= array('unverified',
										'verified',
										'active',
									    'inactive',
										'blocked');
		
		$this->level 			= new Model_UserLevel;

		/*
		$where_cond			= array('name 	!=' => 'administrator',
									'status !=' => 'deleted');
		 * 
		 */
		$where_cond			= array('id' => '99',
									'status !=' => 'deleted');
		/*
		$where_cond				= array('id >='		=> $this->session->get('level_id'),
										'status'	=> 'enable');
		 * 
		 */
		$this->levels			= $this->level->find($where_cond);
		
		$buffers	 = array();

		foreach($this->levels as $row) {
			$buffers[] = $row->id;
		}

		$this->buffers 		= $buffers;

		$this->profile		= new Model_UserProfile;
		$profiles			= $this->profile->find();
		$buffers			= array();

		foreach ($profiles as $row) {
			$buffers[$row->user_id]	= $row;
		}

		$this->profiles		= $buffers;

		$where_user			= array(/*'status != '	=> 'blocked',*/
									'level_id IN'	=> $this->buffers);

		$order_by			= array('name'	=> 'ASC');

		$this->user			= new Model_User;
		$this->users		= $this->user->find($where_user, $order_by);
		
		// Data settings for website
		$settings = Model_Setting::instance()->find(array('status' => 'publish'));
		$buffers  = array();
		foreach ($settings as $setting){
			$buffers[$setting->parameter] = trim(strip_tags($setting->value));
		}
		$settings = $buffers;

		//-- Object that accesed from within all inheritance
		$this->settings = $settings;
		//-- Object that accesed from within all inheritance
		
	}

	public function action_index () {
		$where_cond = array(/*'status = ' 	=> 'active',*/
							'level_id' 	=> '99');

		if ($_POST) {
			$post = new Validation($_POST);

			if (isset($post['field']) || isset($post['keyword'])) {
				$post->rule('keyword', 'regex', array(':value', '/^[a-z0-9_.\s\-]++$/iD'));

				if ($post->check()) {
					if (!in_array($post['field'], $this->_prefilter_keys))
						$where_cond[$post['field'] . ' LIKE'] = $post['keyword'] . '%';
					else
						$where_cond['id IN']	= call_user_func(array($this, '_'.$post['field']), $_POST['keyword']);

					$filters = array('f' => $post['field'],
									 'q' => $post['keyword']);

					$this->session->set($this->_class_name.'_filter', serialize($filters));
				} else if (isset($post['find'])) {
					$this->session->delete($this->_class_name.'_filter');
				}
			}

			if ($this->session->get($this->_class_name.'_filter') !== FALSE) {
				$filters = unserialize($this->session->get($this->_class_name.'_filter'));

				if (in_array($filters['f'], array_keys($this->_search_keys)) && $filters['q'] != '') {
					if (!in_array($filters['f'], $this->_prefilter_keys))
						$where_cond[$filters['f'] . ' LIKE']	= $filters['q'].'%';
					else
						$where_cond['id IN']	= call_user_func(array($this, '_'.$filters['f']), $filters['q']);
				}
			}
		}

		/** Table sorting **/

		$params		= Request::$current->param();
		$sorts		= array('asc', 'desc');

		$sort		= isset($params['id2']) ? $this->id2 : 'name';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN . $this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers = array('email'		=> 'E-mail',
							   'name'		=> 'Name',
							   1			=> 'Gender',
							   2			=> 'Birthday',
							   3			=> 'Country',
							   4			=> 'State',
							   5			=> 'City',
							   6			=> 'Phone',
							   7			=> 'Church',
							   'status'		=> 'Status',
							   'added'		=> 'Added',
							   'modified'	=> 'Modified');

		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);

			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];

			$order_by	= array($sort	=> $order);

			$base_url	= ADMIN . $this->_class_name.'/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
		}

		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';

		$where_cond	= isset($where_cond) ? $where_cond : '';

		$total_rows = $this->user->find_count($where_cond);
		$total_record = $total_rows;
		$listings	= $this->user->find($where_cond, $order_by, $per_page, $offset);
		
		//print_r($where_cond);
		
		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);

			$this->redirect($base_url.$page_index);
			return;
		}

		$this->session->get($this->_class_name.'_index', $base_url.$page_index);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);

		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'total_record'	=> $total_record,
									'profiles'		=> $this->profiles);

		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}
	
	public function action_add () {

		$fields	= array('name'				=> '',
						'email'				=> '',
						'password'			=> '',
						'confirm_password'	=> '',
						'level_id'			=> '',
			
						'user_id'		=> '',
						'gender'		=> '',
						'about'			=> '',
						'country'		=> '',
						'state'			=> '',
						'city'			=> '',
						'address'		=> '',
						'postal_code'	=> '',
						'birthday'		=> '',
						'phone'			=> '',
						'church'		=> '',
						'fax'			=> '',
						'website'		=> '',
						'photo'			=> '');

		$errors	= $fields;

		if ($_POST) {
			$post	= Validation::factory($_POST)
					->rule('name', 'not_empty')
					->rule('name', 'min_length', array(':value', 4))
					->rule('email', 'not_empty')
					->rule('email', 'Valid::email', array(':value'))
					->rule('email', array($this, '_valid_email'), array(':validation', 'email'))
					->rule('password', 'not_empty')
					->rule('confirm_password', 'not_empty')
					->rule('confirm_password', 'matches', array(':validation','confirm_password','password'))
					->rule('level_id', array($this, '_valid_level_id'), array(':validation', ':field', 'level_id'))
					
					->rule('birthday', 'not_empty')
					->rule('phone', 'not_empty');

			if ($post->check()) {
				$fields	= $post->as_array();
				
				/** USER **/
				$params	= array('name'		=> $fields['name'],
								'email'		=> $fields['email'],
								'password'	=> sha1($fields['password']),
								'level_id'	=> ($fields['level_id']!='99') ? '99' : '99',
								'status'	=> 'active');
				
				$id			= Model_User::instance()->add($params);

				/** PROFILE **/
				$file_name	= '';
				$file_mime	= '';
				
				if (isset($_FILES['photo']) && upload::required($_FILES['photo']) && upload::valid($_FILES['photo'])) {
					$file_hash	= md5(mktime() + rand(100, 999));
					$file_data	= pathinfo($_FILES['photo']['name']);

					$file_name	= upload::save('photo', $file_hash.'.'.$file_data['extension'], Lib::config($this->_class_name.'.upload_path'));
					$file_data	= pathinfo($file_name);
					$file_mime	= $_FILES['photo']['type'];

					$file_name	= $file_data['basename'];
				}

				if (!isset($file_mime) || !isset($file_name)) {
					$file_mime	= @$this->profile->file_type;
					$file_name	= @$this->profile->file_name;
				}
				
				$params = array(
								'user_id'		=> $id,
								'gender'		=> $fields['gender'],
								'about'			=> $fields['about'],
								'country'		=> $fields['country'],
								'state'			=> $fields['state'],
								'city'			=> $fields['city'],
								'address'		=> $fields['address'],
								'postal_code'	=> $fields['postal_code'],
								'church'		=> $fields['church'],
								'birthday'		=> $fields['birthday'],
								'phone'			=> $fields['phone'],
								'fax'			=> $fields['fax'],
								'website'		=> $fields['website'],
								'file_type'		=> $file_mime,
								'file_name'		=> $file_name,
								'status'		=>'active' );
				
				$this->profile->user_id = $id;
				$profile_exists			= $this->profile->load();
				
				if (!$profile_exists) {
					$params_id = $this->profile->add($params);
				} else {
					foreach ($params as $var => $val) {
						$this->profile->$var	= $val;
					}

					$this->profile->update();

					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->profile->user_id);
					return;
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

		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'module_menu'	=> $this->_module_menu,
									'class_name'	=> $this->_class_name,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'genders'		=> $this->_genders,
									'levels'	=> $this->levels,
									'statuses'	=> $this->statuses);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content;  ;
	}
	
	public function action_edit () {
		$id						= $this->id1;
		$this->profile->user_id = $id;
		$profile_exists			= $this->profile->load();

		$this->user->id			= $id;
		$this->user->load();

		$this->level->id		= $this->user->level_id;
		$this->level->load();

		$fields = array('user_id'		=> '',
						'gender'		=> '',
						'about'			=> '',
						'country'		=> '',
						'state'			=> '',
						'city'			=> '',
						'address'		=> '',
						'postal_code'	=> '',
						'church'		=> '', 
						'birthday'		=> '',
						'phone'			=> '',
						'fax'			=> '',
						'website'		=> '',
						'photo'			=> '');

		if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
			foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
				$fields[$row_name]	= '';
			}
		}

		$errors = $fields;

		if ($_POST) {
			$post = new Validation($_POST);

			//$post->rule('name', 'not_empty');
			//gender
			//$post->rule('country', 'not_empty');
			//$post->rule('state', 'not_empty');
			//$post->rule('city', 'not_empty');
			//$post->rule('address', 'not_empty');
			//$post->rule('postal_code', 'not_empty');
			
			$post->rule('gender', 'not_empty');
			$post->rule('phone', 'not_empty');
			$post->rule('birthday', 'not_empty');

			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				if (isset($this->_prefs['photo']['file_type']))
					$post->rule($row_name, 'upload::type['.$this->_prefs['photo']['file_type'].']');

				if (isset($this->_prefs['photo']['max_file_size']))
					$post->rule($row_name, 'upload::size['.$this->_prefs['photo']['max_file_size'].']');
			}

			if ($post->check()) {
				$fields = $post->as_array();

				if (isset($fields['delete_photo']) && $fields['delete_photo'] == 1) {
					$file_name	= '';
					$file_mime	= '';
				} else if (isset($_FILES['photo']) && upload::required($_FILES['photo']) && upload::valid($_FILES['photo'])) {
					$file_hash	= md5(mktime() + rand(100, 999));
					$file_data	= pathinfo($_FILES['photo']['name']);

					$file_name	= upload::save('photo', $file_hash.'.'.$file_data['extension'], Lib::config($this->_class_name.'.upload_path'));
					$file_data	= pathinfo($file_name);
					$file_mime	= $_FILES['photo']['type'];

					$file_name	= $file_data['basename'];
				}

				if (!isset($file_mime) || !isset($file_name)) {
					$file_mime	= @$this->profile->file_type;
					$file_name	= @$this->profile->file_name;
				}

				$params = array('user_id'		=> $id,
								'gender'		=> $fields['gender'],
								'about'			=> $fields['about'],
								'country'		=> $fields['country'],
								'state'			=> $fields['state'],
								'city'			=> $fields['city'],
								'address'		=> $fields['address'],
								'postal_code'	=> $fields['postal_code'],
								'church'		=> $fields['church'],
								'birthday'		=> $fields['birthday'],
								'phone'			=> $fields['phone'],
								'fax'			=> $fields['fax'],
								'website'		=> $fields['website'],
								'file_type'		=> $file_mime,
								'file_name'		=> $file_name,
								'status'		=> $this->user->status);

				if (!$profile_exists) {
					$params_id = $this->profile->add($params);
				} else {
					foreach ($params as $var => $val) {
						$this->profile->$var	= $val;
					}
					//print_r($this->profile); exit();
					$this->profile->update();

					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->profile->user_id);
					return;
				}

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$params_id);
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
			$fields = array('gender' 		=> isset($this->profile->gender) ? $this->profile->gender : '',
							'country'		=> isset($this->profile->country) ? $this->profile->country : '',
							'about'			=> isset($this->profile->about) ? $this->profile->about : '',
							'state'			=> isset($this->profile->state) ? $this->profile->state : '',
							'city'			=> isset($this->profile->city) ? $this->profile->city : '',
							'address'		=> isset($this->profile->address) ? $this->profile->address : '',
							'postal_code'	=> isset($this->profile->postal_code) ? $this->profile->postal_code : '',
							'church'		=> isset($this->profile->church) ? $this->profile->church : '',
							'birthday'		=> isset($this->profile->birthday) ? $this->profile->birthday : '',
							'phone'			=> isset($this->profile->phone) ? $this->profile->phone : '',
							'fax'			=> isset($this->profile->fax) ? $this->profile->fax : '',
							'website'		=> isset($this->profile->website) ? $this->profile->website : '');
		}

		$content_vars = array(
							'id'			=> $id,
							'errors'		=> $errors,
							'fields'		=> $fields,
							'module_menu'	=> $this->_module_menu,
							'class_name'	=> $this->_class_name,
							'class_name'	=> $this->_class_name,
							'upload_url'	=> $this->_upload_url,
							'upload_path'	=> $this->_upload_path,	
							'genders'		=> $this->_genders,
							'statuses'	=> $this->statuses,
							'user'		=> $this->user,
							'profile'	=> $this->profile,
							'level'		=> $this->level
						);
		
		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

    public function action_view () {
			$id = $this->id1;
            $this->profile->user_id	= $id;

            if (!$this->profile->load()) {
				$this->session->set('auth_error','Member doesn\'t have profile');
				$this->redirect(ADMIN.$this->_class_name.'/index');
				return;
            }

            $this->user->id			= $id;
            $this->user->load();
			//print_r( $this->user);
            //$this->level->id		= $this->user->level_id;
            $this->level->id		= 99;
			$this->level->load();
			
            $content_vars		= array(
										'class_name' => $this->_class_name,
										'class_name' => $this->_class_name,
										'module_menu'	=> $this->_module_menu,	
										'profile'	=> $this->profile,
										'user'		=> $this->user,
										'level'		=> $this->level);

			$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');

			foreach ($content_vars as $var => $val) {
				$content->$var	= $val;
			}

			$this->template->content		= $content;  ;
    }
	
	public function action_delete(){
		$this->profile->user_id	= $this->id1;
		
		if (!$this->profile->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}
		
		// Set is_deleted / status to TRUE	
		// This is used for only Ajax Request		
		if ($this->request->is_ajax()) {
			// This setting is used for deleting all file included
			if (Model_User::instance()->delete($this->id1)) {
				Model_UserProfile::instance()->delete($this->id1);
				echo 1;
			} else {
				echo 0;
			}	
		} else {
			$this->redirect(ADMIN.$this->_class_name);
		}
		exit();	
	}
	
    public function action_activation () {
		$where_cond = array('status = ' 	=> 'inactive',
							'level_id = ' 	=> '99');

		if ($_POST) {
			$post = new Validation($_POST);

			if ($this->uri->last_segment() == 'select_action' && isset($_POST['check'])) {
				$rows = $_POST['check'];

				foreach ($rows as $row) {
					$this->profile->user_id = $row;

					if (!$this->profile->load())
						continue;

					$this->profile->status = $_POST['select_action'];
					$this->profile->update();
				}

				$redirect_url = (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/activation';
				$this->redirect($redirect_url);
				//$this->redirect(ADMIN.$this->_class_name.'/activation');
				return;
			}

			if (isset($_POST['field']) || isset($_POST['keyword'])) {
				$post->add_callbacks('field', array($this, '_valid_search_key'));

				if ($post->check()) {
					if (!in_array($_POST['field'], $this->_prefilter_keys))
						$where_cond[$_POST['field'] . ' LIKE'] = $_POST['keyword'] . '%';
					else
						$where_cond['id IN']	= call_user_func(array($this, '_'.$_POST['field']), $_POST['keyword']);

					$filters = array('f' => $_POST['field'],
									 'q' => $_POST['keyword']);

					$this->session->set($this->_class_name.'_filter', serialize($filters));
				} else if (isset($_POST['find'])) {
					$this->session->delete($this->_class_name.'_filter');
				}
			}

			if ($this->session->get($this->_class_name.'_filter') !== FALSE) {
				$filters = unserialize($this->session->get($this->_class_name.'_filter'));

				if (in_array($filters['f'], array_keys($this->_search_keys)) && $filters['q'] != '') {
					if (!in_array($filters['f'], $this->_prefilter_keys))
						$where_cond[$filters['f'] . ' LIKE']	= $filters['q'].'%';
					else
						$where_cond['id IN']	= call_user_func(array($this, '_'.$filters['f']), $filters['q']);
				}
			}
		}

		/** Table sorting **/

		$params 	= $this->uri->segment_array(3, TRUE);
		$sorts		= array('asc', 'desc');

		$sort 		= isset($params['sort']) ? $params['sort'] : 'name';
		$order		= isset($params['order']) ? $params['order'] : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($params['page']) ? $params['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		$page_url	= '';
		$base_url	= ADMIN.$this->_class_name.'/activation/index/page/';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers = array(/*'no'			=> 'No',*/
							   'email'		=> 'E-mail',
							   'name'		=> 'Name',
							   'division'	=> 'Division',
							   2			=> 'Country',
							   3			=> 'State',
							   4			=> 'City',
							   5			=> 'Phone',
							   'status'		=> 'Status',
							   'added'		=> 'Added',
							   'modified'	=> 'Modified');

		if (isset($params['sort']) && isset($params['order'])) {
			$headers	= array_keys($table_headers);

			$sort		= (isset($params['sort']) && in_array(strtolower($params['sort']), $headers)) ? strtolower($params['sort']) : $headers[0];
			$order		= (isset($params['order']) && in_array(strtolower($params['order']), $sorts)) ? strtolower($params['order']) : $sorts[0];

			$order_by	= array($sort	=> $order);

			$base_url	= ADMIN . $this->_class_name.'/activation/index/sort/' . $params['sort'] . '/order/' . $params['order'] . '/page/';
		}

		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';

		$where_cond	= isset($where_cond) ? $where_cond : '';

		$total_rows = $this->user->find_count($where_cond);
		$listings	= $this->user->find($where_cond, $order_by, $per_page, $offset);

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);

			$this->redirect($base_url.$page_index);
			return;
		}

		$this->session->get($this->_class_name.'_index', $base_url.$page_index);

		$config		= array('base_url'			=> $base_url,
							'total_items'		=> $total_rows,
							'items_per_page'	=> $per_page,
							'uri_segment'		=> 'page');

		$pagination	= new Pagination($config);

		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $page_index,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'profiles'		=> $this->profiles);

		$content			= View::factory(ADMIN . $this->_class_name.'_activation');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}

		$view				= View::factory(ADMIN .'/base_admin');

		$view->title		= ucwords(str_replace('_', ' ', $this->_class_name)).' Activation Listings';
		$view->content		= $content;

		$view->render(TRUE);
            
	}

	/** PRE FILTER **/

	public function _reverse_date ($value = '') {
		if (strpos($value, '/') != 0)
			return (implode('-', array_reverse(explode('/', $value))));
		else
			return (implode('/', array_reverse(explode('-', $value))));
	}

	public function _country ($value = '') {
		$where_cond	= array('country LIKE'	=> '%'.$value.'%');

		$buffers	= $this->profile->find($where_cond);

		$ids		= array();

		foreach ($buffers as $row) {
			$ids[]	= $row->user_id;
		}

		return $ids;
	}

	public function _state ($value = '') {
		$where_cond	= array('state LIKE'	=> '%'.$value.'%');

		$buffers	= $this->profile->find($where_cond);
		$ids		= array();

		foreach ($buffers as $row) {
			$ids[]	= $row->user_id;
		}

		return $ids;
	}

	public function _city ($value = '') {
		$where_cond	= array('city LIKE'	=> '%'.$value.'%');

		$buffers	= $this->profile->find($where_cond);
		$ids		= array();

		foreach ($buffers as $row) {
			$ids[]	= $row->user_id;
		}

		return $ids;
	}

	public function _phone ($value = '') {
		$where_cond	= array('phone LIKE'	=> '%'.$value.'%');

		$buffers	= $this->profile->find($where_cond);
		$ids		= array();

		foreach ($buffers as $row) {
			$ids[]	= $row->user_id;
		}

		return $ids;
	}
	
	/*** Function Access ***/
	
	// Action for update item status
	public function action_change() {
		//print_r($this->request->post('select_action'));
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');

			$send_data	= (object) array();
			$password = array();
			//$send_email = FALSE;
			foreach ($rows as $row) {
				// get user id 
				$this->user->id = $row;
				
				// get user profile user_id 
				$this->profile->user_id = $row;
				
				if (!$this->user->load()) {
					continue;
				}
				
				// print_r(strpos($this->user->load($row)->password, '-:|:-')); exit();
				
				// ------- Set user password to sha1 ----- start
				if (strpos($this->user->load_password(), '-:|:-') != FALSE
						&& $this->request->post('select_action') == 'active') {
					
					// explode code to match code data
					$password[$row] = explode('-:|:-', (string) $this->user->load_password());
					//print_r($password); exit();
					if (!empty($password[$row][0])) {
						
						// send email data
						//$send_data->password	= $password[$row][0];
						//$send_data->name		= $this->user->name;
						//$send_data->email		= $this->user->email;
						// convert to object
						//$send_data				= Lib::to_object($send_data);
						//print_r($password); exit();
						// update existing user data
						$this->user->reset_password($password[$row][0]);
						// set send email to true
						//$send_email = TRUE;
						
						$content_vars	= array(
												'username'	=> $this->user->name, 
												'password'	=> $password[$row][0],
												'site_name'		=> !empty($this->settings['title_name']) ? $this->settings['title_name'] : '',
												'default_title' => !empty($this->settings['title_default']) ? $this->settings['title_default'] : '',
												'registered_mark'	=> !empty($this->settings['registered_mark']) ? $this->settings['registered_mark'] : '',
												);

						$email_view		= View::factory('email/member_approval');

						foreach ($content_vars as $var3 => $val3) {
							$email_view->$var3	= $val3;
						}
						//print_r($email_view); exit();
						$to			= $this->user->email;  // Address can also be array('to@example.com', 'Name')
						$from		= Lib::config('site.email_address');
						$subject	= Lib::config('site.email_verification_subject');

						// Render email html view
						$message	= $email_view->render();			

						// Send email to member
						usleep(10000);
						$sent = Email::send($to, $from, $subject, $message, TRUE);
						
					}	
					
					// send email if status is active and send_email is true
					//if ($this->request->post('select_action') == 'active' && $send_email == TRUE && !empty($send_data)) {
						// send email with data
						//$this->_send_email ($send_data);
					//}
					
				}
				// ------- Set user password to sha1 ----- end				
								
				// set user status
				$this->user->status = $this->request->post('select_action');
				$this->user->update();
				
				if (!$this->profile->load()) {
					continue;
				}
				
				// set member status
				$this->profile->status = $this->request->post('select_action');
				$this->profile->update();
					
			}
			//print_r($send_data); exit();
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';

			$this->redirect($redirect_url);
			
		} else {
			
			$this->redirect(ADMIN . $this->_class_name);
			
		}
		
	}
	
	public function _send_email($data = ''){
		
		//print_r($data); exit();
		
		if (empty($data))
			return;
		
		/*** Send an email message to member for activated accounts ***/

		$content_vars	= array(
								'username'	=> $data->name, 
								'password'	=> $data->password,
								'site_name'		=> !empty($this->settings['title_name']) ? $this->settings['title_name'] : '',
								'default_title' => !empty($this->settings['title_default']) ? $this->settings['title_default'] : '',
								'registered_mark'	=> !empty($this->settings['registered_mark']) ? $this->settings['registered_mark'] : '',
								);

		$email_view		= View::factory('email/member_approval');

		foreach ($content_vars as $var3 => $val3) {
			$email_view->$var3	= $val3;
		}
		//print_r($email_view); exit();
		$to			= $data->email;  // Address can also be array('to@example.com', 'Name')
		$from		= Lib::config('site.email_address');
		$subject	= Lib::config('site.email_verification_subject');

		// Render email html view
		$message	= $email_view->render();			

		// Send email to member
		$sent = Email::send($to, $from, $subject, $message, TRUE);
		
		return $sent;
	}

		/** CALLBACKS **/

	public function _valid_email (Validation $array, $field) {
		if (!Valid::email($array[$field]))			
			return $array->error($field, 'email_exists');
	}
		
	public function _unique_username (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		
		$member = !Model_User::unique_username($array[$field]);
		
		if ($member)
			return $array->error($field, 'unique_name');
	}
	
	public function _unique_email (Validation $array, $field) {
		if (!Valid::email($array[$field]))
			return $array->error($field, 'email_exists');
	}

	public function _valid_level_id (Validation $array, $field) {
		$level_check	= $this->user->find(array('level_id'=>$field));
		$level_exists	= count($level_check);
		
		if (!$level_exists)
			return true;//$array->error($field, 'invalid_level_id');
	}

}
