<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Api extends Controller_Themes_Default {

	protected $id1;
	protected $id2;
	protected $id3;
	protected $id4;

	public function before () {
		parent::before();

		// Get url routes value
        $this->id1 = Request::$current->param('id1');
        $this->id2 = Request::$current->param('id2');
        $this->id3 = Request::$current->param('id3');
		$this->id4 = Request::$current->param('id4');
		
	}

	public function action_index() {
		
		// Check security token
		//if (!Security::check(base64_decode($this->id1))) {
			
			// Redirect to home if invalid
			//$this->redirect(BS_URL);
		//}
		
		// Check session id
		if (base64_decode($this->id1) !== $this->session->id()) {
			
			// Redirect to home if invalid
			$this->redirect(BS_URL);
		}
		
		
		// Set data fields
		$fields	= array(
						'_url_default'		=> '',
						'_url_custom'		=> '',
						'_keyword_custom'	=> ''
					);
		
		// Set default variables
		$return = array();
		$errors	= $fields;	
		
		if ($this->request->is_ajax() && $this->request->post()) {
			
			// Delay for a second
			//usleep(50000);
						
			// Set POST for validation
			$post = new Validation($this->request->post());

			// Set Field rules for validation
			$post->rule('_url_default','Valid::url', array(':value'));
			$post->rule('_url_custom','Valid::url', array(':value'));
			
			$post->rule('_keyword_custom', array($this, '_unique_keyword'), array(':validation', '_keyword_custom'));
			$post->rule('_keyword_custom','regex', array(':value','/^[a-zA-Z0-9_.\@\&\-]++$/iD'));
			
			$post->rule('_keyword_custom', 'min_length', array(':value', 5));
			$post->rule('_keyword_custom', 'max_length', array(':value', 10));

			if ($post->check()) {
				
				// Set posts fields 
				$fields	= $post->as_array();

				// Set default vars
				$result		= '';
				$message	= '';
				$url		= '';
				$title		= '';
				$clicks		= '';
					
				// Check url keywords default post form
				if ($this->request->post('_url_default') != '') {

					// Check if url is already exists in database
					$data	= Model_Url::instance ()->load_by_url($this->request->post('_url_default'));

					if (!empty($data)) {

						// Return keywords if existed
						$result		= 1;
						$message	= URL::site() . $data->keywords;
						$url		= $this->request->post('_url_default');
						$title		= ($data->title) ? $data->title : '';
						$clicks		= ($data->clicks) ? $data->clicks : '';

					} else {

						// Set random keywords
						/** THIS $random SHOULD BE CHECKED IN DATABASE IF KEYWORDS ALREADY EXISTED **/
						$random		= Text::random('alnum', rand(4,5));

						// Return new keywords
						$result		= 1;
						$message	= URL::site() . $random;
						$url		= $this->request->post('_url_default');
						$title		= Lib::_get_curl_title_tags($this->request->post('_url_default'));

						// Set params
						$params = array(
							'keywords'	=> $random,
							'url'		=> $this->request->post('_url_default'),
							'title'		=> $title,
							'timestamp'	=> date('Y-m-d h:m:s'),
							'ip'		=> Request::$client_ip,
							'clicks'	=> 0,
							'status'	=> 1
						);

						// Add params for database insert
						Model_Url::instance()->add($params);

					}
				
				// If user uses custom keywords post form
				} else 

				//  Check url keywords custom post
				if ($this->request->post('_url_custom') != '' && $this->request->post('_keyword_custom') != '') {

					// Return keywords if existed
					$result		= 1;
					$message	= URL::site() . $this->request->post('_keyword_custom');
					$url		= $this->request->post('_url_custom');
					$title		= Lib::_get_curl_title_tags($this->request->post('_url_custom'));
					$clicks		= '';

					// Set params
					$params = array(
						'keywords'	=> $this->request->post('_keyword_custom'),
						'url'		=> $this->request->post('_url_custom'),
						'title'		=> $title,
						'timestamp'	=> date('Y-m-d h:m:s'),
						'ip'		=> Request::$client_ip,
						'clicks'	=> 0,
						'status'	=> 1
					);

					// Add params for database insert
					Model_Url::instance()->add($params);

				} else {
					
					// Set message to clients
					$result		= 2;
					$message	= 'Please Provide Url..';
				}
				
				// Set message result to client
				$return['result']	= $result;
				$return['message']	= $message;
				$return['url']		= $url;
				$return['title']	= $title;
				$return['clicks']	= $clicks;
				
				// Set keywords data to cookies
				$cookies = unserialize(base64_decode(Cookie::get('keywords')));

				// Get keyword message
				$cookies_data = $return['message'];
				
				if (!empty($cookies) && !in_array($cookies_data, $cookies)) {
				
					// Set temporary data
					$buffers = array_merge($cookies,(array) $cookies_data);
					
					// Get keywords cookies if available and merge it with the new ones
					// $name, $value, $expiration = NULL
					Cookie::set('keywords', 
							base64_encode(serialize($buffers)),time() + 31536000);
				} else {
					// Set first time cookies keywords if not available before
					Cookie::set('keywords', 
							base64_encode(serialize((array) $cookies)),time() + 31536000);
				}
				
					
			} else {
				
				// Set fields and errors messages
				$fields		= Arr::overwrite($fields, $post->as_array());
				$errors 	= Arr::overwrite($errors, $post->errors('validation'));
				$buffers	= $errors;
				
				foreach ($errors as $row_key => $row_val) {
					if ($row_val != '') {
						$buffers[$row_key]	= $row_val;
					} else {
						$buffers[$row_key]	= $row_val;
					}
					
					// Set arrays of invalid messages
					$return['result'] = 0;
					$return['message'] = $buffers;
				}

			}
			
			// Returned in JSON data
			echo json_encode($return);	
			
		}
		
		exit();
		
	}
	
	public function _unique_keyword (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		// Find keywords for unique value
		$keyword = Model_Url::instance()->load_by_keywords($array[$field]);
		
		if ($keyword)
			return $array->error($field, 'unique_keyword');
	}
	
	public function _token_check (Validation $array, $field) {
		if (!isset($array[$field]))
			return;
		
		// Check token for valid value
		$token = !Security::check(base64_decode($array[$field]));
		
		if ($token)
			return $array->error($field, 'token_check');
	}
}
