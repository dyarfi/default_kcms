<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_UserProfile extends Model_Database {
	protected $table = 'user_profiles'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;
        
	public function __construct() {
		parent::__construct();

		$this->_model_vars	= array('user_id'				=> 0,
									'gender'				=> '',
									'about'					=> '',
									'division'				=> '',
									'country'				=> '',
									'state'					=> '',
									'city'					=> '',
									'address'				=> '',
									'postal_code'			=> '',
									'church'				=> '',
									'birthday'				=> '',
									'phone'					=> '',
									'mobile_phone'			=> '',
									'fax'					=> '',
									'website'				=> '',
									'file_type'				=> '',
									'file_name'				=> '',
									'status'				=> 'active',
									'added'					=> 0,
									'modified'				=> 0);
		
		$this->db		= Database::instance();
		$this->tbl_name	= $this->table;		
        $this->prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
		$this->table	= (!empty($this->prefix)) ? $this->prefix . $this->table : $this->table;
	}

	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();

		return self::$_instance;
	}

	public function install () {
		$insert_data		= FALSE;

		if (!$this->db->list_tables($this->table)) {
			$insert_data	= TRUE;

		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
				. '`user_id` INT(11) UNSIGNED NOT NULL,'
				. '`gender` ENUM(\'male\',\'female\') NOT NULL DEFAULT \'male\', '
				. '`about` TEXT NULL, '
				. '`division` VARCHAR(64) NULL, '
				. '`country` VARCHAR(64) NULL, '
				. '`state` VARCHAR(64) NULL, '
				. '`city` VARCHAR(128) NULL, '
				. '`address` TEXT NULL, '
				. '`postal_code` VARCHAR(8) NULL, '
				. '`church` VARCHAR(255) NULL, '
				. '`birthday` DATE, '
				. '`phone` VARCHAR(16) NULL, '
				. '`mobile_phone` VARCHAR(16) NULL, '
				. '`fax` VARCHAR(16) NULL, '
				. '`website` VARCHAR(255) NULL, '
				. '`file_type` VARCHAR(64) NULL, '
				. '`file_name` VARCHAR(48) NULL, '
				. '`status` ENUM(\'active\', \'inactive\', \'blocked\') NOT NULL DEFAULT \'inactive\', '
				. '`added` INT(11) UNSIGNED NOT NULL, '
				. '`modified` INT(11) UNSIGNED NOT NULL, '
				. 'INDEX (`user_id`, `phone`) '
				. ') ENGINE=MYISAM';
	
			$this->db->query('CREATE',$sql);
		}

        if(!$this->db->query('SELECT', 'SELECT * FROM `'.$this->table.'` LIMIT 0 , 1;'))
			$insert_data	= TRUE;
        
		if ($insert_data) {			
			$sql = 'INSERT INTO `'. $this->table .'` VALUES '
					.'(\'1\', \'male\', \'Superadmin of this Website\', \'\', \'Indonesia\', \'DKI Jakarta\', \'Jakarta\', \'Jl. Gading Putih 1 F2 No. 4\', \'14240\', \'\', \'2010/09/06\', \'1234\', \'\', \'0\', \'-\', \'image/jpeg\', \'78d57b4b5a0c6048b75bb0c9d91a8392.jpg\', \'active\', \'1283760138\', \'1283831030\'), '

					.'(\'2\', \'male\', \'Administrator of this Website\', \'\', \'Indonesia\', \'DKI Jakarta\', \'Jakarta\', \'Jl. Gading Putih 1 F2 No. 4\', \'14240\', \'\', \'2010/09/06\', \'1234\', \'\', \'0\', \'-\', \'image/jpeg\', \'78d57b4b5a0c6048b75bb0c9d91a8392.jpg\', \'active\', \'1283760138\', \'1283831030\'), '
					.'(\'3\', \'male\', \'User of this Website\', \'\', \'Jakarta\', \'\', \'\', \'Jl. Pulomas Barat 1 No. 31\', \'\', \'\', \'0000/00/00\', \'1234\', \'\', \'\', \'\', \'image/jpeg\', \'a8a484572c007e1e17648ae2c7ad629c.jpg\', \'active\', \'1285152397\', \'0\'), '
					.'(\'4\', \'male\', \'\', \'\', \'Jakarta\', \'\', \'\', \'Jl. Pulomas Barat 1 No. 31\', \'\', \'\', \'0000/00/00\', \'081807244697\', \'\', \'\', \'\', \'image/jpeg\', \'eb068fc7204f01f8cd25375b42fc6953.jpg\', \'active\', \'1285152397\', \'1326110970\'), '
					.'(\'5\', \'male\', \'\', \'\', \'mimipopo\', \'\', \'\', \'Jl. Pulomas Barat 1 No. 31\', \'\', \'\', \'0000/00/00\', \'081807244697\', \'\', \'\', \'\', \'image/jpeg\', \'eb068fc7204f01f8cd25375b42fc6953.jpg\', \'active\', \'1285152397\', \'1326110970\'), '
					.'(\'111\', \'male\', \'\', \'Web Developer\', \'\', \'\', \'\', \'\', \'\', \'\', \'0000/00/00\', \'\', \'\', \'\', \'\', \'\', \'\', \'active\', \'1333442128\', \'1333442192\'), '
					.'(\'110\', \'male\', \'\', \'Web Developer\', \'\', \'\', \'\', \'\', \'\', \'\', \'0000/00/00\', \'\', \'\', \'\', \'\', \'\', \'\', \'active\', \'1333441986\', \'1333442058\');';
			
			
			$this->db->query('INSERT',$sql);
		}
		
		return $this->db->list_tables($this->table);
	}

	public function load ($user_id = '') {
		$return_object	= TRUE;
		
		if ($user_id == '') {
			$return_object	= FALSE;
			$user_id		= $this->user_id;
		}

		$objects	= $this->find(array('user_id' => $user_id), '', 1);

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
	
	public function add ($params = '') {
		if (!is_array($params)) return;

		$params['added']	= time();

		unset($this->_model_vars['user_id']);
		$this->_model_vars['user_id']		= $this->get_last_id() + 1;
		
		$params	= array_merge($this->_model_vars, $params);
		
		$query = DB::insert($this->tbl_name, array_keys($params))->values(array_values($params))->execute();

		if (count($query) !== FALSE) {
			$insert_id	= $this->get_last_id();
			//Model_ModuleList::instance()->add_user_level_field($insert_id);
			//Model_ModulePermission::instance()->add_user_level_field($insert_id);
		} else {
			return FALSE;
		}

		return $insert_id;
	}
	
	public function get_last_id () {
		$where_cond		= array('user_id !='	=> 99);
		$order_by		= array('user_id'		=> 'DESC');
		$last_id		= $this->find($where_cond, $order_by, 1);
		
		if (!isset($last_id[0]))
			return 1;
			
		return $last_id[0]->user_id;
	}

	public function update () {
		$this->modified	= time();
		$object_vars	= get_object_vars($this);
		
		unset($object_vars['_model_vars'], $object_vars['db']);
		
		$object_vars = Arr::overwrite($this->_model_vars,$object_vars);
		//print_r($object_vars); exit();
		$result = DB::update($this->tbl_name)->set($object_vars)->where('user_id', '=', $this->user_id)->execute();
		
		return $result;
	}

	public function delete ($user_id = '') {
		if ($user_id == '')
			$user_id	= $this->user_id;
		
		$result		= DB::delete($this->tbl_name)->where('user_id', '=', $user_id)->execute();
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
			$object			= new Model_UserProfile();

			$object_vars	= get_object_vars($row);

			unset($object_vars['_model_vars'], $object_vars['db']);

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

	public function full_level_access ($id) {
		$where_cond	= array('id'		=> $id);

		$access		= $this->find($where_cond, '', 1);

		if (!isset($access[0]))
			return FALSE;

		return $access[0]->full_backend_access;
	}
	
	public function query ($query = '',$object=FALSE) {
		if($query == '')
			return FALSE;
		$query = $this->db->query(Database::SELECT, $query, $object);
		if($query !== FALSE)
			return $query;
		else
			return FALSE;	
	}
}