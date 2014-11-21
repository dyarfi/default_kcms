<?php defined('SYSPATH') or die('No direct script access.');

return array(

	// Application defaults
	'default' => array(
		'current_page'      => array('source' => 'mixed', 'key' => 'page'), // source: "query_string" ,"mixed" or "route"
		'total_items'       => 0,
		'items_per_page'    => 10,
		'view'              => 'pagination/floating',
		'auto_hide'         => TRUE,
		'first_page_in_url' => FALSE,
	),

);
