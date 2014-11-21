<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_Article extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;

	protected $_upload_path;
	protected $_upload_url;

	protected $article;
	protected $category;
	protected $categories;
	protected $file;
	protected $statuses;
	
	public function before () {
		// Get parent before method
        parent::before();
		
		$category				= new Model_ArticleCategory();
		$file					= new Model_ArticleFile();
		$this->_class_name		= $this->controller;
		
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_search_keys		= array('subject'		=> 'Subject',
										'name'			=> 'Name',
										'category_id'	=> 'Category',
										'status'		=> 'Status');
		
		$this->_prefilter_keys	= array('category_id');
		$this->_prefs			= (Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_class_name.'.'.$this->_class_name.'_fields') : array();
		$this->_upload_path		= (Lib::config($this->_class_name.'.upload_path') !== NULL) ? Lib::config($this->_class_name.'.upload_path') : array();
		$this->_upload_url		= (Lib::config($this->_class_name.'.upload_url') !== NULL) ? Lib::config($this->_class_name.'.upload_url') : array();
		
		$this->_headline		= Lib::config('article.headline');
		$this->article			= new Model_Article();
		$this->category			= new Model_ArticleCategory();
		$this->file				= new Model_ArticleFile();
		
		$categories				= $category->find();
		$buffers				= array();
		foreach ($categories as $row) {
			$buffers[$row->id]	= $row;
		}
		$this->categories		= $buffers;
		$this->statuses			= array('publish',
										'unpublish',
										'draft');
		unset($category, $categories, $buffers);
	}
	
	public function action_index () {
		$category_id		= !empty($this->id1) ? $this->id1 : '';
		$this->category->id	= $category_id;
		if ($category_id != '' && !$this->category->load())
			$category_id	= '';
		$where_cond	= array('status !='	=> 'deleted');
		if ($category_id != '')
			$where_cond['category_id']	= $category_id;
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
		$sort		= isset($params['id2']) ? $this->id2 : 'order';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[1];
		$order_by	= array($sort 	=> $order);
		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;
		$table_headers	= array('subject'		=> 'Subject',
								'category_id'	=> 'Category',
								'order'			=> 'Order',
								'headline'		=> 'Headline',
								'view'			=> 'Views',
								'count'			=> 'Like',								
								'status'		=> 'Status',
								'publish_date'	=> 'Publish Date',
								'added'			=> 'Added',
								'modified'		=> 'Modified');
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
		$field		= isset($filters['f']) ? $filters['f'] : '';
		$keyword	= isset($filters['q']) ? $filters['q'] : '';
		$where_cond	= isset($where_cond) ? $where_cond : '';
		$default_order	= array('category_id'=>'asc');
		$order_by		= array_merge($default_order,$order_by);
		$total_rows	= count($this->article->find($where_cond));
		$total_record = $total_rows;
		$listings	= $this->article->find($where_cond, $order_by, $per_page, $offset);
		$pagination	= Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 ));
		/** Views **/
		$content_vars		= array('listings'		=> $listings,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'max_order'		=> $this->article,
									'min_order'		=> $this->article,
									'field'			=> $field,
									'keyword'		=> $keyword,
									'order'			=> $order,
									'sort'			=> $sort,
									'page_url'		=> $page_url,
									'page_index'	=> $offset,
									'params'		=> $params,
									'pagination'	=> $pagination,
									'categories'	=> $this->categories,
									'headline'		=> $this->_headline,	
									'total_record'	=> $total_record,
									'category_id'	=> $category_id);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_index');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		
		//$required_js					= array('js/jquery.fancyzoom-min.js');
		//$required_css					= array('js/css/jquery.fancyzoom-min.css');
		//$this->template->js			= $required_js;
		//$this->template->css			= $required_css;
		
		$this->template->content		= $content; 
	}
	
	public function action_add () {
		// Category Id
		$category_id = $this->id1;
		$fields	= array('category_id'		=> '',
						'name'				=> '',
						'subject'			=> '',
						'synopsis'			=> '',
						'text'				=> '',
						'publish_date'		=> '',
						'unpublish_date'	=> '',
						'allow_comment'		=> '',
						'tags'				=> '',
						'headline'			=> '',
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
		$fields['category_id']	= $category_id;
		if ($_POST) {
			if ($_FILES)
				$post	= new Validation(array_merge($_POST, $_FILES));
			else
				$post	= new Validation($_POST);
                $post->rule('subject', 'not_empty');
                $post->rule('subject', 'min_length', array(':value', 4));
                $post->rule('name', 'not_empty');
                $post->rule('name', 'min_length', array(':value', 4));
                //$post->rule('news_date', 'not_empty');
                $post->rule('text', 'not_empty');
                $post->rule('text', 'min_length', array(':value', 4));
                $post->rule('status', 'not_empty');
                $post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
				
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['optional']) && !$row_params['optional']) {
						//$post->add_rules($row_name, 'upload::required');
						//$post->rule($row_name, 'upload::valid');
					//}
					//if (isset($row_params['file_type']))
						//$post->rule($row_name, 'upload::type['.$row_params['file_type'].']');
					//if (isset($row_params['max_file_size']))
						//$post->rule($row_name, 'upload::size['.$row_params['max_file_size'].']');
					if (Upload::type($post[$row_name],explode(',',$row_params['file_type'])) !== 1)
						continue;
				}
			}
			//$post->add_callbacks('name', array($this, '_unique_name'));
			if ($post->check()) {
				$fields	= $post->as_array();
				$where_cond	= array('category_id'		=> $fields['category_id']);
				$last_order	= $this->article->find_count($where_cond) + 1;
				if (isset($fields['order']) && $fields['order'] < $last_order) {
					$where_cond	= array('category_id'	=> $fields['category_id'],
										'order >='		=> $fields['order']);
					$articles	= $this->article->find($where_cond);
					foreach ($articles as $row) {
						$row->order	= $row->order + 1;
						$row->update();
					}
					unset($articles);
				}
				$params	= array('category_id'		=> $fields['category_id'],
								'name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'synopsis'			=> isset($fields['synopsis']) ? $fields['synopsis'] : '',
								'text'				=> $fields['text'],
								'attribute'			=> isset($fields['attribute']) ? $fields['attribute'] : '',
								'publish_date'		=> isset($fields['publish_date']) ? $fields['publish_date'] : '0000-00-00',
								'unpublish_date'	=> isset($fields['unpublish_date']) ? $fields['unpublish_date'] : '0000-00-00',
								'allow_comment'		=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
								'tags'				=> isset($fields['tags']) ? $fields['tags'] : '',
								'headline'			=> isset($fields['headline']) ? $fields['headline'] : 0,
								'order'				=> !empty($fields['order']) ? $fields['order'] : $this->article->last_order($fields['category_id']) + 1,
								'user_id'			=> (isset($this->acl->user->id)) ? $this->acl->user->id : 0,					
								'status'			=> $fields['status']);
				$id		= $this->article->add($params);
				if ($id !== FALSE && isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
						//if (!upload::required($_FILES[$row_name]) || !upload::valid($_FILES[$row_name]))
							//continue;
						if (!Upload::not_empty($post[$row_name]) || 
								!Upload::type($post[$row_name],explode(',',$row_params['file_type'])) || 
								!Upload::valid($post[$row_name])) {
								continue;
						}
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						if ($file_name != '' && isset($this->_prefs['uploads'][$row_name]['image_manipulation'])) {
							$params			= array('article_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							$this->file->add($params);
						}
					}
				}
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
		$where_cond			= array('status !='		=> 'deleted');
		$order_by			= array('category_id'	=> 'ASC',
									'order'			=> 'ASC');
		$orders				= $this->article->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'categories'	=> $this->categories,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
									'orders'		=> $orders,
									'headline'		=> $this->_headline,
									'statuses'		=> $this->statuses);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_add');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content;  
	}
	
	public function action_view ($id = '', $category_id = '') {
		$this->article->id	= $this->id1;
		if (!$this->article->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		/** Views **/
		if ($this->article->category_id != 0) {
			$category		= $this->category->load($this->article->category_id);
			$category		= HTML::chars($category->title, TRUE);
		} else {
			$category		= 'This Article doesn\'t have category';
		}
		if ($this->article->order == 0) {
			$order			= '-';
		} else if ($this->article->order != 1) {
			$where_cond		= array('category_id'	=> $this->article->category_id,
									'order'			=> ($this->article->order - 1));
			$pages		= $this->article->find($where_cond, '', 1);
			if (isset($pages[0]))
				$order		= 'After category '.HTML::chars($pages[0]->subject, TRUE);
			else
				$order		= '';
		} else {
			$order			= 'At the beginning';
		}
		$where_cond			= array('article_id'	=> $this->article->id);
		$files				= $this->file->find($where_cond);
		$buffers			= array();
		foreach ($files as $row) {
			$buffers[$row->field_name]	= $row;
		}
		$files				= $buffers;
		unset($buffers);
		/** Generate Thumbnails **/
		Lib::_auto_image_manipulation($this->_upload_path, $this->file, $this->_prefs);
		$content_vars		= array('article'		=> $this->article,
									'category'		=> $category,
									'files'			=> $files,
									'upload_path'	=> $this->_upload_path,
									'upload_url'	=> $this->_upload_url,
									'order'			=> $order,
									'class_name' 	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,	
									'headline'		=> $this->_headline,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									'category_id'	=> $category_id);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_view');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}
	
	public function action_edit () {
		$this->article->id	= $this->id1;
		$id	= $this->id1;
		$category_id = $this->id2;
		if (!$this->article->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		$fields	= array('category_id'		=> '',
						'name'				=> '',
						'subject'			=> '',
						'synopsis'			=> '',
						'text'				=> '',
						'publish_date'		=> '',
						'unpublish_date'	=> '',
						'allow_comment'		=> '',
						'tags'				=> '',
						'headline'			=> '',
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
                $post->rule('subject', 'not_empty');
                $post->rule('subject', 'min_length', array(':value', 4));
                $post->rule('name', 'not_empty');
                $post->rule('name', 'min_length', array(':value', 4));
                //$post->rule('news_date', 'not_empty');
                $post->rule('text', 'not_empty');
                $post->rule('text', 'min_length', array(':value', 4));
                $post->rule('status', 'not_empty');
                //$post->rule('name', array($this, '_unique_name'), array(':validation', 'name'));
				
			if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
				foreach ($this->_prefs['uploads'] as $row_name => $row_params) {
					//if (isset($row_params['file_type']))
						//$post->rule($row_name, 'upload::type['.$row_params['file_type'].']');
					//if (isset($row_params['max_file_size']))
						//$post->rule($row_name, 'upload::size['.$row_params['max_file_size'].']');
					if (!File::exts_by_mime($post[$row_name]['type']))
						continue;
				}
			}
			
			//$post->rule('name', array($this, '_unique_name'));
			
			if ($post->check()) {
				$fields	= $post->as_array();
				
				$fields['order']	= ($fields['category_id'] != $fields['_category_id']) ? $this->article->last_order($fields['category_id']) + 1 : $fields['_category_id'];
								
				$params	= array('category_id'		=> $fields['category_id'],
								'name'				=> $fields['name'],
								'subject'			=> $fields['subject'],
								'synopsis'			=> isset($fields['synopsis']) ? $fields['synopsis'] : '',
								'text'				=> $fields['text'],
								'attribute'			=> isset($fields['attribute']) ? $fields['attribute'] : '',
								'publish_date'		=> isset($fields['publish_date']) ? $fields['publish_date'] : '0000-00-00',
								'unpublish_date'	=> isset($fields['unpublish_date']) ? $fields['unpublish_date'] : '0000-00-00',
								'allow_comment'		=> isset($fields['allow_comment']) ? $fields['allow_comment'] : 0,
								'tags'				=> isset($fields['tags']) ? $fields['tags'] : '',
								'headline'			=> isset($fields['headline']) ? $fields['headline'] : 0,
								'order'				=> !empty($fields['order']) ? $fields['order'] : '',
								'status'			=> $fields['status']);
				foreach ($params as $var => $val) {
					$this->article->$var	= $val;
				}
				$this->article->update();
				if (isset($this->_prefs['show_upload']) && $this->_prefs['show_upload'] && isset($this->_prefs['uploads'])) {
					$where_cond			= array('article_id'	=> $this->article->id);
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
						$file_hash	= md5(time() + rand(100, 999));
						$file_data	= pathinfo($post[$row_name]['name']);
						
						$file_name	= Lib::_upload_to($post[$row_name], $file_hash.'.'.$file_data['extension'], $this->_upload_path, 0755);
						$file_data	= pathinfo($file_name);
						$file_mime	= $post[$row_name]['type'];
						if (!isset($files[$row_name])) {
							$params			= array('article_id'	=> $id,
													'field_name'	=> $row_name,
													'file_name'		=> $file_data['basename'],
													'file_type'		=> $file_mime,
													'caption'		=> isset($fields[$row_name.'_caption']) ? $fields[$row_name.'_caption'] : '');
							$this->file->add($params);
						} else {
							$this->file->id	= $files[$row_name]->id;
							$this->file->load();
							$params			= array('article_id'	=> $id,
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
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->article->id.'/'.$category_id);
				else
					$this->redirect(ADMIN.$this->_class_name.'/view/'.$this->article->id);
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
			$fields	= array('category_id'		=> $this->article->category_id,
							'name'				=> $this->article->name,
							'subject'			=> $this->article->subject,
							'synopsis'			=> $this->article->synopsis,
							'text'				=> $this->article->text,
							'publish_date'		=> ($this->article->publish_date != '0000-00-00') ? $this->article->publish_date : '',
							'unpublish_date'	=> ($this->article->unpublish_date != '0000-00-00') ? $this->article->unpublish_date : '',
							'allow_comment'		=> $this->article->allow_comment,
							'tags'				=> $this->article->tags,
							'headline'			=> $this->article->headline,
							'order'				=> $this->article->order,
							'status'			=> $this->article->status);
			$where_cond			= array('article_id'	=> $this->article->id);
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
		$orders				= $this->article->find($where_cond, $order_by);
		$content_vars		= array('errors'		=> $errors,
									'fields'		=> $fields,
									'article'		=> $this->article,
									'categories'	=> $this->categories,
									'module_menu'	=> $this->_module_menu,				
									'class_name'	=> $this->_class_name,
									'class_name'	=> $this->_class_name,
									'files'			=> $files,
									'orders'		=> $orders,
									'statuses'		=> $this->statuses,
									'headline'		=> $this->_headline,
									'readable_mime'	=> Lib::config($this->_class_name.'.readable_mime'),
									'category_id'	=> $category_id);
		$content_vars		= array_merge($content_vars, $this->_prefs);
		//$content			= array();
		$content			= View::factory($this->_class_name.'/backend/'.$this->_class_name.'_edit');
		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		$this->template->content		= $content; 
	}
	
	// Action for ordering item list
	public function action_order() {
		if (empty($this->id2) && empty($this->id3))
			continue;
		if ($this->id1 == 'up' && $this->id2 && $this->id3 && $this->id4) {
			$where_cond = array('order'=>$this->id3 + 1, 'category_id'=>$this->id4);
			$order_by   = array('order'=>'asc');
			$order_default = $this->article->find($where_cond,$order_by,1);
			if (!empty($order_default)) {
				foreach ($order_default as $default) {
					$default->order  = ($default->order != $this->article->first_order($default->category_id)) ? $default->order - 1 : 0;
					$default->update();
				}
			}
			$this->article->id = $this->id2;
			$this->article->load();
			$this->article->order = $this->id3 + 1;
			$this->article->update();
		} 
		if ($this->id1 == 'down' && $this->id2 && $this->id3 && $this->id4) {
			$where_cond = array('order'=>$this->id3 - 1,'category_id'=>$this->id4);
			$order_by	= array('order'=>'asc');
			$order_default = $this->article->find($where_cond,$order_by,1);
			if (!empty($order_default)) {
				foreach ($order_default as $default) {
					$default->order  = ($default->order != $this->article->last_order($default->category_id)) ? $default->order + 1 : 1;
					$default->update();
				}
			}
			$this->article->id = $this->id2;
			$this->article->load();
			$this->article->order = $this->id3 - 1;
			$this->article->update();
		}
		if ($this->request->is_ajax()) {
			echo $this->id2;
			exit();
		}
		$this->redirect(ADMIN.$this->_class_name);		
	}
	
	public function action_delete () {
		
		$this->article->id	= $this->id1;
		
		//print_r($this->id1); exit();
		if (!$this->article->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/error/invalid_request');
			return;
		}
		
		if($this->article->delete($this->id1)) {
			$where_cond		= array('article_id'=>$this->id1);
			$articlefiles	= Model_ArticleFile::instance()->find($where_cond);
			foreach ($articlefiles as $files) {
				if (is_readable($this->_upload_path.$files->file_name)) {
					//$this->file->delete($files->id);
					@unlink($this->_upload_path.$files->file_name);
					$this->file->delete($files->id);
				}
			}
			echo 1;
		} else {
			echo 0;	
		}
		
		exit();
		
//		$this->session->set_flash('function_delete', 'success');
//
//		$redirect_url	= ($this->acl->previous_url != '') ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
//
//		$this->redirect($redirect_url);
	}
	
	public function action_download() {
		$files		= $this->id1;
		$where_cond	= array('file_name'	=> $files);
		$files		= $this->file->find($where_cond);
		foreach ($files as $row) {
			Lib::_download_file_force('',$this->_upload_url.$row->file_name);
		}
	}
	/*** Function Access ***/
	// Action for update item status
	public function action_change() {	
		if ($this->request->post('check') !='') {
			$rows	= $this->request->post('check');
			foreach ($rows as $row) {
				$this->article->id	= $row;
				if (!$this->article->load())
					continue;
				$this->article->status	= $this->request->post('select_action');
				$this->article->update();
			}
			$redirect_url	= (strstr($this->acl->previous_url,ADMIN)) ? $this->acl->previous_url : ADMIN.$this->_class_name.'/index';
			$this->redirect($redirect_url);
		} else {	
			$this->redirect(ADMIN.$this->_class_name);			
		}
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
//	public function _unique_name (Validation $array, $field) {
//		if (isset($this->page->name) && $this->page->name == $array[$field])
//			return;
//
//		$where_cond		= array('name'	=> $array[$field]);
//		$name_exists	= ($this->page->find_count($where_cond) != 0);
//
//		if ($name_exists)
//			$array->error($field, 'name_exists');
//	}
	public function _valid_status (Validation $array, $field) {
		if (!in_array($array[$field], $this->statuses))
			$array->error($field, 'invalid_status');
	}
	/** PRE FILTER **/
	public function _safe_html_name ($value = '') {
		return htmlentities($value);
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
	/** CALLBACKS **/  
    public function _unique_name (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		$name = Model_Article::instance()->find_count(array('name' => $array[$field]));
		if ($name)
			return $array->error($field, 'unique_name');
	}
}
