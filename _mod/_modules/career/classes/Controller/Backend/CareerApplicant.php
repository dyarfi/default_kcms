<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_CareerApplicant extends Controller_Backend_BaseAdmin {
	protected $_module_name;
	protected $_class_name;
	protected $_search_keys;
	protected $_prefs;
	
	protected $_upload_path;
	protected $_upload_url;

	protected $_uid;
	protected $_did;
	protected $_settings;

	public function before () {
		// Get parent before method
        parent::before();
		
		$this->_module_name		= 'career';
		$this->_class_name 		= $this->controller;
		$this->_module_menu		= $this->acl->module_menu;
		
		$this->_search_keys		= array('name'			=> 'Name',
										'email'			=> 'Email',
										//'gender'		=> 'Gender',
										//'address'		=> 'Address',
										'phone'			=> 'Phone',
										'status'		=> 'Status',
										/*'language'		=> 'Language',*/);
		
		$this->_prefs			= (Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') !== NULL) ? Lib::config($this->_module_name.'.'.$this->_class_name.'_fields') : array();
		
		$this->career_applicant	= new Model_CareerApplicant;
		$this->career			= new Model_Career;
		$this->division			= new Model_CareerDivision;

		$this->language			= array('indonesia',
										'english');

		$this->statuses			= array('publish',
										'unpublish');
				
		
		//** Data settings for website
		$settings = Model_Setting::instance()->find(array('status' => 1));
		$buffers  = array();
		foreach ($settings as $setting){
			$buffers[$setting->parameter] = $setting->value;
		}
		$settings = $buffers;
		
		//** Object that accesed from within all inheritance
		$this->_settings = $settings;

		// Grade config
		$this->grade = (Lib::config($this->_module_name.'.grade') !== NULL) ? Lib::config($this->_module_name.'.grade') : array();
		// Gender config
		$this->gender = (Lib::config($this->_module_name.'.gender') !== NULL) ? Lib::config($this->_module_name.'.gender') : array();
		// Marital config
		$this->marital_status = (Lib::config($this->_module_name.'.marital_status') !== NULL) ? Lib::config($this->_module_name.'.marital_status') : array();
		
		//-- User id from user login session 'user_id'
		$this->_uid = $this->session->get('user_id');
		
		$user		= Model_User::instance()->find(array('id'=>$this->_uid));
		
		//-- User Division Id from user data get
		//$this->_did = $user[0]->division_id;
		
		//print_t($this->_did); exit();
	}

	public function action_index () {
		//error_reporting(E_ALL);
		// Echo done
		//echo date('H:i:s') . " Done writing file.\r\n";
		
		/** Table Career Division **/
		$where_cond		= array('status' => 1);
		if (!empty($this->_did)) {
			$where_cond = array_merge($where_cond,array('id'=>$this->_did));
		}
		
		$division_buffer	= array();
		$career_division	= Model_CareerDivision::instance()->find($where_cond,'');
		foreach($career_division as $division) {
			$division_buffer[$division->id] = $division;
		}
		$division = $division_buffer;
		unset($where_cond, $division_buffer);
		
		/** Table Career **/
		$where_cond		= array('status' => 1);
		if (!empty($this->_did)) {
			$where_cond = array_merge($where_cond,array('division_id'=>$this->_did));
		} else {
			$where_cond = array('status' => 'publish');
		}
		
		$career_buffer	= array();
		$career			= Model_Career::instance()->find($where_cond,'');
		$career_ids		= $career;
		foreach($career as $career_val) {
			$career_buffer[$career_val->id] = $career_val;
		}
		$career = $career_buffer;
		unset($where_cond, $division_buffer, $career_buffer);
		
		$ids_buffer = array();
		foreach ($career_ids as $career_id){
			$ids_buffer[$career_id->id] = $career_id->id;
		}
		$career_ids = $ids_buffer;
		unset($where_cond, $ids_buffer);
		
		// Listing applicant status
		$where_cond	= array('status !='	=> 'deleted');
		if (!empty($this->_did)) {
			$where_cond = array_merge($where_cond,array('career_id IN' => $career_ids));
		}
		
		/** Find & Multiple change status **/

		if ($_POST) {
			$post	= new Validation($_POST);

			if ($this->id1 == 'select_action' && isset($_POST['check'])) {
				$rows	= $_POST['check'];

				foreach ($rows as $row) {
					$this->career_applicant->id	= $row;

					if (!$this->career_applicant->load())
						continue;

					$this->career_applicant->status	= $_POST['select_action'];
					$this->career_applicant->update();
				}

				$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';

				$this->redirect($redirect_url);
				return;
			}

			if (isset($posts['field']) || isset($post['keyword'])) {
				$post->rule('field', array($this, '_valid_search_key'), array(':validation', 'field'));
				$post->rule('keyword', 'regex', array(':value', '/^[a-z0-9_.\s\-]++$/iD'));

				if ($post->check()) {
					$where_cond[$_POST['field'] . ' LIKE']	= $_POST['keyword'] . '%';

					$filters	= array('f'	=> $_POST['field'],
										'q'	=> $_POST['keyword']);

					$this->session->set($this->_class_name.'_filter', serialize($filters));
				} else if (isset($_POST['find'])) {
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

		$sort		= isset($params['id2']) ? $this->id2 : 'added';
		$order		= isset($params['id4']) ? $this->id4 : $sorts[0];
		$order_by	= array($sort 	=> $order);

		$page_index	= isset($_GET['page']) ? $_GET['page'] - 1: 0;
		$per_page	= Lib::config('admin.item_per_page');
		//$per_page	= 10;
		$page_url	= isset($_GET['page']) ? '?page='.$_GET['page'] : '';
		$base_url	= ADMIN.$this->_class_name;
		$offset		= ($page_index == 0) ? '' : $page_index * $per_page;

		$table_headers	= array(
								'name'				=> 'Name', 
								'career_id'			=> 'Career Applying', 
								'email'				=> 'Email', 
								'gender'			=> 'Gender', 
								'marital_status'	=> 'Marital Status', 
								'phone'				=> 'Phone', 
								//'address'			=> 'Address', 
								'birth_date'		=> 'Birth Date', 
								'education_grade'	=> 'Education', 
								'cv_file'			=> 'CV',
								'photo'				=> 'Photo',
								'status'			=> 'Status',
								'added'				=> 'Added', 
								//'modified'			=> 'Modified'					
							);

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

		$total_rows	= $this->career_applicant->find_count($where_cond);
		$total_record = $total_rows;
		$listings	= $this->career_applicant->find($where_cond, $order_by, $per_page, $offset);

		/** Store index url **/

		if (count($listings) == 0 && $total_rows != 0) {
			$page_index	= ceil($total_rows / $per_page);

			$this->redirect($base_url.$page_index);
			return;
		}

		$this->session->get($this->_class_name.'_index', $base_url.$page_index);

		/** Initialize pagination **/

		$pagination = Pagination::factory(array(
				'total_items' 		=> $total_rows,
				'items_per_page' 	=> $per_page,
			 )
		);


		/** Views **/

		$content_vars		= array(
									'grade'			 => $this->grade,
									'gender'		 => $this->gender,
									'marital_status' => $this->marital_status,
									'career'		=> $career,
									'division'		=> $division,
									'listings'		=> $listings,
									'total_record'  => $total_record,
									'table_headers'	=> $table_headers,
									'statuses'		=> $this->statuses,
									'search_keys'	=> $this->_search_keys,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu,
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
		
		$this->template->content		= $content; 
	}

	public function action_add () {
		$fields	= array('name'			=> '',
						'sent_to'		=> '',
						'ref_no'		=> '',
						'start_date'	=> '',
						'end_date'		=> '',
						'synopsis'		=> '',
						'qualification'	=> '',
						'language'		=> '',
						'status'		=> '');

		$errors	= $fields;

		if ($_POST) {
			$post	= new Validation($_POST);

			if ($post->validate()) {
				$fields	= $post->as_array();

				$params	= array('name'				=> $fields['name'],
								'sent_to'			=> $fields['sent_to'],
								'ref_no'			=> $fields['ref_no'],
								'start_date'		=> $this->_reverse_date($fields['start_date']),
								'end_date'			=> $this->_reverse_date($fields['end_date']),
								'synopsis'			=> $fields['synopsis'],
								'qualification'		=> $fields['qualification'],
								'language'			=> !empty($fields['language']) ? $fields['language'] : '',
								'status'			=> $fields['status']);

				$id		= $this->career_applicant->add($params);

				$this->session->set_flash('function_add', 'success');

				if (isset($_POST['add_another'])) {
					$this->redirect(ADMIN.$this->_class_name.'/add');
					return;
				}

				$this->redirect(ADMIN.$this->_class_name.'/view/'.$id);
				return;
			} else {
				$fields		= arr::overwrite($fields, $post->as_array());
				$errors 	= arr::overwrite($errors, $post->errors());
				$buffers	= $errors;

				foreach ($errors as $row_key => $row_val) {
					if ($row_val != '')
						$buffers[$row_key]	= Lib::config('admin.error_field_open').Kohana::lang('validation.'.$errors[$row_key]).Lib::config('admin.error_field_close');
					else
						$buffers[$row_key]	= $row_val;
				}

				$errors		= $buffers;
			}
		}

		/** Views **/

		$content_vars		= array('errors'	=> $errors,
									'fields'	=> $fields,
									'language'	=> $this->language,
									'statuses'	=> $this->statuses,
									'module_name' => $this->_module_name,
									'class_name' => $this->_class_name,
									'module_menu' => $this->_module_menu);

		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_add');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
	}

	public function action_view () {
		$this->career_applicant->id	= $this->id1;

		if (!$this->career_applicant->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}

		/** Views **/

		$content_vars		= array('career'		=> $this->career_applicant,
									'module_name'	=> $this->_module_name,
									'class_name'	=> $this->_class_name,
									'module_menu'	=> $this->_module_menu);
		
		$content_vars		= array_merge($content_vars, $this->_prefs);
		
		$content			= View::factory($this->_module_name.'/backend/'.$this->_class_name.'_view');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->content		= $content; 
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
	
	public function action_exportdata () {

		// Career Applicant CSV Export data mode
		$file_path	= Lib::config('career.upload_path_cv');
		$file_url	= Lib::config('career.upload_url_cv');
		
		$division_title	= 'Administrator';
		$where_cond		= array('status' => 'publish');
		
		if (!empty($this->_did)) {
			$where_cond = array_merge($where_cond, array('division_id'=>$this->_did)); 
			$division = $this->division->find(array('id'=>$this->_did));
			if (!empty($division))
				$division_title	= $division[0]->subject;
		}
		
		$careers			= $this->career->find($where_cond,'');
		
		// ** Find career id defined that already in division id
		$career_ids			= array();
		foreach ($careers as $career){
			$career_ids[$career->id] = $career->id;
		}
		$careers_ids = $career_ids;
		unset($where_cond);
		
		$where_cond = array('status'=>'publish');
		if (!empty($careers_ids) && is_array($careers_ids)) {
			$where_cond = array_merge($where_cond, array('career_id IN'=>$careers_ids)); 
		}
		$listings			= $this->career_applicant->find($where_cond,array('added'=>'DESC'));
		
		$buffers			= array();
		$division_buffer	= array();
		foreach($careers as $career) {
			$buffers[$career->id] = $career;
		}
		$career = $buffers;	
		
		// Header Content Type
		//header('Content-Type: application/csv');
		header ("Expires: Mon, 28 Oct 2008 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/vnd.ms-excel");
		header ("Content-Disposition: attachment; filename=\"Applicant-Listing-".date("dmY").".xls" );
		header ("Content-Description: Generated Report" );
		header ("Content-Type: application/force-download");
		
		//header('Content-Disposition: attachment;filename="Applicant_Listing_'.date('dmY').'.csv"');
		// Header table data
		//echo "Name,Applying Career,Email,Gender,Marital Status,Phone,Birth Date,Birth Place,Education,CV,Photo,Available Date,Status,Added\n";
		// Listing table data
		
		echo '<table border="1">
				<tr>
					<th style="text-align:left"><b>'.$this->_settings['title_name'].' </b></th>
					<th style="text-align:left">'.$division_title.'</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>			
				<tr>
					<th style="text-align:left"><b>Date</b></th>
					<th style="text-align:left">'.date("F j, Y, g:i a").'</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>						
				</tr>
				<tr>
					<th style="text-align:left"><b>Number of Data</b></th>
					<th style="text-align:left">'.count($listings).'</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<tr id="titles">
					<th class="tableTd" bgcolor="#CCCCCC">Name</th>
					<th class="tableTd" bgcolor="#CCCCCC">Applying Career</th>
					<th class="tableTd" bgcolor="#CCCCCC">Email</th>
					<th class="tableTd" bgcolor="#CCCCCC">Gender</th>
					<th class="tableTd" bgcolor="#CCCCCC">Marital Status</th>
					<th class="tableTd" bgcolor="#CCCCCC">Phone</th>
					<th class="tableTd" bgcolor="#CCCCCC">Birth Date</th>
					<th class="tableTd" bgcolor="#CCCCCC">Birth Place</th>
					<th class="tableTd" bgcolor="#CCCCCC">Education</th>
					<th class="tableTd" bgcolor="#CCCCCC">CV</th>
					<th class="tableTd" bgcolor="#CCCCCC">Photo</th>
					<th class="tableTd" bgcolor="#CCCCCC">Available Date</th>
					<th class="tableTd" bgcolor="#CCCCCC">Status</th>
					<th class="tableTd" bgcolor="#CCCCCC">Added</th>
				</tr>';
					
		foreach ($listings as $row) {
			$row->phone			  = !empty($row->phone) ? str_replace(array(';',','), '', '\''. $row->phone) : "-";
			$row->birth_date	  = !empty($row->birth_date) ? str_replace(array(';',','), '', $row->birth_date) : "-";					
			$row->birth_place	  = !empty($row->birth_place) ? str_replace(array(';',','), '', $row->birth_place) : "-";
			$row->education_grade = !empty($this->grade[$row->education_grade]) ? $this->grade[$row->education_grade] : "-";
			$row->cv_file		  = !empty($row->cv_file) && is_file($file_path.$row->cv_file) ? url::site(ADMIN.$this->uri->segment(2).'/download/'.$row->cv_file) : "-";
			$row->photo			  = !empty($row->photo) && is_file($file_path.$row->photo) ? url::site().$file_url.$row->photo : "-";
			$row->available_date  = !empty($row->available_date) ? str_replace(array(';',','), '', $row->available_date) : "-";
			
			/*
			echo $row->name.","
				.$career[$row->career_id]->subject.","
				.$row->email.","
				.$this->gender[$row->gender].","
				.$this->marital_status[$row->marital_status].","
				.$row->phone.","
				.$row->birth_date.","					
				.$row->birth_place.","
				.$row->education_grade.","
				.$row->cv_file.","
				.$row->photo.","
				.$row->available_date.","
				.$row->status.","
				.date('d-m-Y',$row->added)."\n";
			*/
			
			echo '<tr>';
			echo '<td class="tableTdContent">'.$row->name.'</td>';
			echo '<td class="tableTdContent">'.$career[$row->career_id]->subject.'</td>';
			echo '<td class="tableTdContent">'.$row->email.'</td>';
			echo '<td class="tableTdContent">'.$this->gender[$row->gender].'</td>';
			echo '<td class="tableTdContent">'.$this->marital_status[$row->marital_status].'</td>';
			echo '<td class="tableTdContent">'.$row->phone.'</td>';
			echo '<td class="tableTdContent">'.$row->birth_date.'</td>';
			echo '<td class="tableTdContent">'.$row->birth_place.'</td>';
			echo '<td class="tableTdContent">'.$row->education_grade.'</td>';
			echo '<td class="tableTdContent">'.$row->cv_file.'</td>';
			echo '<td class="tableTdContent">'.$row->photo.'</td>';
			echo '<td class="tableTdContent">'.$row->available_date.'</td>';
			echo '<td class="tableTdContent">'.$row->status.'</td>';
			echo '<td class="tableTdContent">'.date('d-m-Y',$row->added).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	public function action_delete ($id = '') {
		$this->career_applicant->id	= $id;

		if (!$this->career_applicant->load()) {
			$this->redirect(ADMIN.$this->_class_name.'/index');
			return;
		}

		$this->career_applicant->status	= 'deleted';
		if($this->career_applicant->update())
			echo 1;
		else
			echo 0;
		
		exit;

//		$this->session->set_flash('function_delete', 'success');
//
//		$redirect_url	= (ACL::instance()->previous_url != '') ? ACL::instance()->previous_url : ADMIN.$this->_class_name.'/index';
//
//		$this->redirect($redirect_url);
	}

	/** CALLBACKS **/

	public function _unique_name (Validation $array, $field) {
		if (isset($this->career_applicant->name) && $this->career_applicant->name == $array[$field])
			return;

		$where_cond		= array('name'		=> $array[$field],
								'status !='	=> 'deleted');
		$name_exists	= ($this->career_applicant->find_count($where_cond) != 0);

		if ($name_exists)
			$array->error($field, 'name_exists');
	}

	public function _reverse_date ($value = '') {
		if (strpos($value, '/') != 0)
			return (implode('-', array_reverse(explode('/', $value))));
		else
			return (implode('/', array_reverse(explode('-', $value))));
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
