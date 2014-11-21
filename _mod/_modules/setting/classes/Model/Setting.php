<?php defined('SYSPATH') or die('No direct script access.');

class Model_Setting extends Model_Database {
    protected $table = 'settings'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'				=> 0,
									'parameter'			=> '',
									'alias'				=> '',
									'value'				=> '',
									'is_system'			=> '',
									'status'			=> '',
									'added'				=> 0,
									'modified'			=> 0);

		$this->db		= Database::instance();
		$this->tbl_name = $this->table;
		$this->prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
        $this->table	= (!empty($this->prefix)) ? $this->prefix . $this->table : $this->table;
	}

	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();

		return self::$_instance;
	}

	public function install () {
		$insert_data	= FALSE;

		if (!$this->db->list_tables($this->table)) 
               $insert_data	= TRUE;
                
		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
					. '`id` int(11) unsigned NOT NULL AUTO_INCREMENT,'
					. '`parameter` varchar(255) DEFAULT NULL,'
					. '`alias` varchar(255) DEFAULT NULL,'
					. '`value` varchar(255) DEFAULT NULL,'
					. '`is_system` tinyint(1) DEFAULT 1,'
					. '`status` enum(\'publish\',\'unpublish\',\'deleted\') DEFAULT \'publish\','
					. '`added` int(11) DEFAULT NULL,'
					. '`modified` int(11) DEFAULT NULL,'
					. 'PRIMARY KEY (`id`),'
					. 'KEY `name` (`parameter`,`status`)'
					. ') ENGINE=MyISAM DEFAULT CHARSET=latin1';

		$this->db->query('CREATE', $sql);

		if ($insert_data) {
			$sql	= 'INSERT INTO `'.$this->table.'` (`id`, `parameter`, `alias`, `value`, `status`, `added`, `modified`) VALUES '
						.'(1, \'email_marketing\', \'Email Marketing\', \'marketing@default.com\', \'publish\', 1334835773, NULL), '
						.'(2, \'email_administrator\', \'Email Administrator\', \'defrian@default.com\', \'publish\', 1334835773, 1336122482), '
						.'(3, \'email_hrd\', \'Email HRD\', \'hrd@default.com\', \'publish\', 1334835773, NULL), '
						.'(4, \'email_info\', \'Email Info\', \'info@default.com\', \'publish\', 1334835773, NULL), '
						.'(5, \'email_template\', \'Email Template\', \'&dash;\', \'publish\', 1334835773, NULL), '					
						.'(6, \'maintenance_template\', \'Maintenance Mode Template\', \'<h2>The site is off for <span><h1>MAINTENANCE</h1></span></h2>\', \'publish\', 1334835773, NULL), '						
						.'(7, \'contactus_address\', \'Contact Address\', \'&dash;\', \'publish\', 1334835773, NULL), '
						.'(8, \'contactus_gmap\', \'GMaps Location\', \'http://maps.google.com/maps?q=-6.217668,106.812992&num=1&t=m&z=18\', \'publish\', 1334835773, NULL), '
						.'(9, \'no_phone\', \'Number Phone\', \'(021) 522.3715\', \'publish\', 1334835773, NULL), '
						.'(10, \'no_fax\',  \'Number Fax\', \'(021) 522.3718\', \'publish\', 1334835773, NULL), '
						.'(11, \'title_default\', \'Website Title Default\', \'We build on solid foundation, effective, construction and visual appeal\', \'publish\', NULL, NULL), '
						.'(12, \'title_name\', \'Company Title Name\', \'PT. Default (Web Agency in Jakarta)\', \'publish\', NULL, 1336118568), '	
						.'(13, \'language\', \'Default Language\', \'en\', \'publish\', NULL, 1336118568), '	
						.'(14, \'counter\', \'Site Counter\', \'123\', \'publish\', NULL, 1336118568), '
						.'(15, \'copyright\', \'Copyright\', \'© 2012 COMPANY NAME COPYRIGHT. All Rights Reserved.\', \'publish\', NULL, 1336118568), '
						.'(16, \'site_name\', \'Site Name\', \' Default <br/> PT. Default (Web Agency in Jakarta).\', \'publish\', NULL, 1336118568), '
						.'(17, \'site_quote\', \'Quote\', \'We provide solution for your Websites\', \'publish\', NULL, 1336118568), '
						.'(18, \'site_description\', \'Website Description\', \'We provide solution for your Company Website \', \'publish\', NULL, 1336118568), '						
						.'(19, \'socmed_facebook\', \'Facebook\', \'http://facebook.com\', \'publish\', NULL, 1336118568), '
						.'(20, \'socmed_twitter\', \'Twitter\', \'http://twitter.com\', \'publish\', NULL, 1336118568), '
						.'(21, \'socmed_gplus\', \'Google Plus\', \'http://plus.google.com\', \'publish\', NULL, 1336118568), '
						.'(22, \'socmed_linkedin\', \'LinkedIn\', \'http://linkedin.com\', \'publish\', NULL, 1336118568), '
						.'(23, \'socmed_pinterest\', \'Pinterest\', \'http://pinterest.com\', \'publish\', NULL, 1336118568), '
						.'(24, \'registered_mark\', \'Registered\', \'We provide solution for your Websites\', \'publish\', NULL, 1336118568),'
						.'(25, \'google_analytics\', \'Analytics\', \'Code Snippet\', \'publish\', NULL, 1336118568), '
						.'(26, \'ext_link\', \'Ext Link\', \'http://www.apb-career.net\', \'publish\', NULL, 1336118568);';

			$this->db->query('INSERT',$sql);
		}

		return $this->db->list_tables($this->table);
	}

	public function load ($id = '') {
		$return_object	= TRUE;

		if ($id == '') {
			$return_object	= FALSE;
			$id				= $this->id;
		}

		$objects	= $this->find(array('id' => $id), '', 1);

		if (count($objects) == 1) {
			if ($return_object) {
				return $objects[0];
			} else {
				$vars	= array_keys($this->_model_vars);

				foreach ($vars as $var) {
					$this->$var	= $objects[0]->$var;
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	public function load_by_parameter ($parameter) {
		$where_cond	= array('parameter'		=> $parameter,
							'status !='		=> 'deleted',
							'status'		=> 'publish');

		if ($this->find_count($where_cond) == 0)
			unset($where_cond['status !=']);

		$objects		= $this->find($where_cond, '', 1);

		return (isset($objects[0])) ? $objects[0] : FALSE;
	}

	public function count_visitor (){	
		
		if (Session_Cookie::instance()->get('session') != session_id()) {
			//print_r(Session_Cookie::instance()->get('session'));	
			$counter = $this->load_by_parameter('counter');
			$counter->value = $counter->value + 1;
			$counter->update();
		}
		Session_Cookie::instance()->set('session', session_id());
	
	}
	
	public function add ($params = '') {
		if (!is_array($params)) return;

		$params['added']	= time();

		unset($this->_model_vars['id']);

		$params	= array_merge($this->_model_vars, $params);
		
		$query = DB::insert($this->tbl_name, array_keys($params))->values(array_values($params))->execute();

		if (count($query) !== FALSE)
			$insert_id	= mysql_insert_id();
		else
			return FALSE;

		return $insert_id;
	}

	public function update () {
		$this->modified	= time();
		$object_vars	= get_object_vars($this);
		
		unset($object_vars['_model_vars'], $object_vars['db']);
		
		$object_vars = Arr::overwrite($this->_model_vars,$object_vars);
		
		$result = DB::update($this->tbl_name)->set($object_vars)->where('id', '=', $this->id)->execute();
		
		return $result;
	}

	public function delete ($id = '') {
		if ($id == '')
			$id	= $this->id;

		$where_cond	= array('id'	=> $id);
		$result		= $this->db->delete('setting', $where_cond);

		$this->db->clear_cache(TRUE);

		return $result;
	}

	public function find ($where_cond = '', $order_by = '', $limit = '', $offset = '') {
		/** Build where query **/

		if ($where_cond != '') {
			if (is_array($where_cond) && count($where_cond) != 0) {
				$buffers	= array();

				$operators	= array('LIKE',
									'IN',
									'!=',
									'>=',
									'<=',
									'>',
									'<',
									'=');

				foreach ($where_cond as $field => $value) {
					$operator	= '=';

					if (strpos($field, ' ') != 0)
						list($field, $operator)	= explode(' ', $field);

					if (in_array($operator, $operators)) {
						$field		= '`'.$field.'`';

						if ($operator == 'IN' && is_array($value))
							$buffers[]	= $field.' '.$operator.' (\''.implode('\', \'', $value).'\')';
						else
							$buffers[]	= $field.' '.$operator.' \''.$value.'\'';
					} else if (is_numeric($field)) {
						$buffers[]	= $value;
					} else {
						$buffers[]	= $field.' '.$operator.' \''.$value.'\'';
					}
				}

				$where_cond	= implode(' AND ', $buffers);
			}
		}

		$sql_order = ''; 
		if ($order_by != '') {
			$sql_order = ' ORDER BY '; 
			$i 	   = 1;
			foreach ($order_by as $order => $val) {
				$split = ($i > 1) ? ', ' : ''; 
				$sql_order .= ' '. $split .' `'. $order.'` '.$val.' ';
				$i++;
			}
			$order_by  = $sql_order;
		}
		
		$sql_limit = ''; 
		if ($limit != '' && $offset != '') {
			$offset    = $offset . ','; 
			$sql_limit = 'LIMIT '. $offset . $limit; 
		}
		else if ($limit != '') {
			$sql_limit = 'LIMIT '. $limit; 
		}
		$limit = $sql_limit;
		
		if ($where_cond != '') {
			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'` WHERE '. $where_cond . $order_by . $limit, TRUE)->as_array();
		}
		else {
			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'`' . $order_by . $limit, TRUE)->as_array();
		}
		
		$returns	= array();

		foreach ($rows as $row) {
			$object			= new Model_Setting();

			$object_vars	= get_object_vars($row);

			foreach ($object_vars as $var => $val) {
				$object->$var	= $val;
			}

			$returns[]		= $object;

			unset($object, $vars);
		}

		return $returns;
	}

	public function find_count ($where_cond = '') {
		/** Build where query **/

		if ($where_cond != '') {
			if (is_array($where_cond) && count($where_cond) != 0) {
				$buffers	= array();

				$operators	= array('LIKE',
									'IN',
									'!=',
									'>=',
									'<=',
									'>',
									'<',
									'=');

				foreach ($where_cond as $field => $value) {
					$operator	= '=';

					if (strpos($field, ' ') != 0)
						list($field, $operator)	= explode(' ', $field);

					if (in_array($operator, $operators)) {
						$field		= '`'.$field.'`';

						if ($operator == 'IN' && is_array($value))
							$buffers[]	= $field.' '.$operator.' (\''.implode('\', \'', $value).'\')';
						else
							$buffers[]	= $field.' '.$operator.' \''.$value.'\'';
					} else if (is_numeric($field)) {
						$buffers[]	= $value;
					} else {
						$buffers[]	= $field.' '.$operator.' \''.$value.'\'';
					}
				}

				$where_cond	= implode(' AND ', $buffers);
			}
			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'` WHERE '. $where_cond, TRUE)->count();
		} else {
			$rows = sizeof($this->find());
		}
		return $rows;
	}
	
	public function counter () {
		//print_r(Session::instance()->as_array());
		if (Session::instance()->get('ip_address') !='' 
				/*&& Session::instance()->get('user_agent') != ''*/
					&& strstr(Request::detect_uri(),'admin-cp') == '') {
			
			//print_r(Request::$client_ip);
			//print_r(Request::$user_agent);
			
			// This will be the update of seeting counter data
			if(Request::$client_ip != Session::instance()->get('ip_address')
					&& Request::$user_agent != Session::instance()->get('user_agent')) {

				$counter = $this->load_by_parameter('counter');
				$counter->value = $counter->value + 1;
				$counter->update();
				
			}
			
		} else {			
			Session::instance()->set('ip_address', Request::$client_ip);
			Session::instance()->set('user_agent', Request::$user_agent);
		}
	}
	
	public function query ($query = '') {
		if($query == '')
			return FALSE;
		
		$query = $this->db->query($query);
		
		if($query !== FALSE)
			return $query;
		else
			return FALSE;	
	}
 }

