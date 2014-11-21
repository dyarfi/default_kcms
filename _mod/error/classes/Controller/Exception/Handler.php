<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Exception_Handler extends Controller_Template {

    public function before()
    {
        $this->auto_render = TRUE;

        $this->template = View::factory();
        $this->template->page = URL::site(rawurldecode(Request::$initial->uri()));

        // Internal request only!
        if (Request::$initial !== Request::$current)
        {
            if ($message = rawurldecode($this->request->param('message')))
            {
                $this->template->message = $message;
            }
        }
        else
        {
            $this->request->action(404);
        }

        $this->response->status((int) $this->request->action());
    }

    public function action_404()
    {
        $view = Kohana::$config->load('errors.views.404');
        $this->template->set_filename($view);
		$this->template->page_title				= '404 - Page Not Found';
		$this->template->meta_keywords			= '';
		$this->template->meta_description		= '';
		$this->template->meta_copyright			= '';
		$this->template->styles					= array();
		$this->template->scripts				= array();

        // Here we check to see if a 404 came from our website. This allows the
        // webmaster to find broken links and update them in a shorter amount of time.
        if (isset ($_SERVER['HTTP_REFERER']) AND strstr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== FALSE)
        {
            // Set a local flag so we can display different messages in our template.
            $this->template->local = TRUE;
        }

        // HTTP Status code.
        $this->response->status(404);
    }

    public function action_500()
    {
        $view = Kohana::$config->load('errors.views.500');
        $this->template->set_filename($view);
		$this->template->page_title				= '500 - Internal Server Error';
		$this->template->meta_keywords			= '';
		$this->template->meta_description		= '';
		$this->template->meta_copyright			= '';
        /*$this->template->title					= 'Internal Server Error';*/
		$this->template->styles					= array();
		$this->template->scripts				= array();
    }

    public function action_503()
    {
        $view = Kohana::$config->load('errors.views.503');
        $this->template->set_filename($view);
		$this->template->page_title				= '503 - Maintenance Mode';
		$this->template->meta_keywords			= '';
		$this->template->meta_description		= '';
		$this->template->meta_copyright			= '';
        /*$this->template->title					= 'Maintenance Mode';*/
		$this->template->styles					= array();
		$this->template->scripts				= array();
    }

}