<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Backend_Maintenance extends Controller_Themes_DefaultMaintenance {

	public function before () {
		parent::before();
	}
	
	public function action_index () { 		
		if (empty($this->is_maintenance))
			$this->redirect (URL::site());		
	}
			
}