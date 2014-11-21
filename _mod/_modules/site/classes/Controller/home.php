<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Home extends Controller_Themes_Default {

	protected $id1;
	protected $id2;
	protected $id3;
	protected $id4;

	public function before () {
		parent::before();

        $this->id1 = Request::$current->param('id1');
        $this->id2 = Request::$current->param('id2');
        $this->id3 = Request::$current->param('id3');
		$this->id4 = Request::$current->param('id4');
		
		// Get meta tags content from curl -- sample code
		/*
		$options = array(
				'url'=>'http://localhost/pemulihan/',
				'meta'=>array('description','copyright','author'));
		$results = Lib::_get_curl_meta_tags($options);
		print_r($results['keywords']);
		print_r($results['description']);
		*/
		
		// Get title from curl -- sample code
		/*
		$results = Lib::_get_curl_title_tags('http://localhost/pemulihan/');
		print_r($results);
		*/
		//Session_Cookie::instance()->destroy('keywords');
		//print_r(Session_Cookie::instance()->get('keywords'));
		
		//Session_Cookie::instance()->destroy('keywords');
		//Cookie::delete('keywords');
		//Cookie::salt('keywords', URL::site());
		//print_r(unserialize(Cookie::get('keywords')));
				
		//print_r(unserialize(Cookie::get('keywords')));
		$sizes	= 'L';
		$colors = 'Blue';
		
		$options = array(
						'size' => $sizes, 
						'color' => $colors
					);
		
        $data = array(
				'cart' => array(
				   'id'      => 1,
				   'qty'     => 1,
				   'price'   => '20.000',
				   'name'    => 'Product',
				   'options' => $options
				 )
            );

        //echo '<pre>'; print_r($data); exit();
        //$this->cart->insert($data);
		//print_r(Cart::instance()->insert($data));
		//print_r(Cart::instance()->contents());
		//print_r(Cart::instance()->destroy());
		
	}
    
	public function action_index () {
		
		$content_vars		= array('empty'=>TRUE);

		$content			= View::factory('site/home_page');

		foreach ($content_vars as $var => $val) {
			$content->$var	= $val;
		}
		
		$this->template->meta_keywords		= Lib::_explode_keywords('simple shorturl sharing links shortened');
		
		$this->template->meta_description	= 'Default.Co - ' . strip_tags('URL Shortener | http://default.co/');
		
		$this->template->page_title	= $this->data['title_default'] . ' | ' . Lib::config('site.title');
		
		$this->template->content	= $content; 
		
	}
	
	public function action_redirect () {
		
		if(!empty($this->id1)) {
			// Check keyword for redirects
			$redirect = Model_Url::instance()->load_by_keywords($this->id1);
			
			if(!empty($redirect)) {
				
				// Set params to add in Urllogs
				$params = array(
						'click_time'	=> date("Y-m-d H:i:s"),
						'shorturl'		=> $this->id1,
						'referrer'		=> Request::$current->referrer() ? Request::$current->referrer() : URL::site(),
						'user_agent'	=> Request::$user_agent,
						'ip_address'	=> Request::$client_ip,
						'country_code'	=> '',
						'status'		=> 1,
						);
				
				$insert		= Model_UrlLog::instance()->add($params);
				
				// Update UrlLog clicks
				$redirect->clicks = $redirect->clicks + 1;
				$redirect->update();
			
				// Update Url clicks
				if($updateurl_status	= Model_Url::instance()->load_by_keywords($this->id1)) {				
					$updateurl_status->clicks = $redirect->clicks;
					$updateurl_status->update();
				}
				
				// Redirect user to original url
				$this->redirect($redirect->url);
				
			} else {
				
				// Set message to user if the url is not exist
				$this->session->set('result',
						'No link available for <abbr class="red" title="'.URL::site().$this->id1.'">"'.URL::site().$this->id1.'"</abbr>');
				//$this->redirect(BS_URL);
			}
		}
		
	}
	
	public function after() {
		parent::after();
		
	}
}
