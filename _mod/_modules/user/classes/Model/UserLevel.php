<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_UserLevel extends Model_Database {
	protected $table = 'user_levels'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $tbl_prefix;
	protected static $_instance;
        
	public function __construct() {
		parent::__construct();

		$this->_model_vars	= array('id'					=> 0,
									'name'					=> '',
									'backend_access'		=> 0,
									'full_backend_access'	=> 0,
									'is_system'				=> 0,
									'status'				=> '',
									'added'					=> 0,
									'modified'				=> 0);

		$this->db		= Database::instance();
		$this->tbl_name	= $this->table;		
        $this->tbl_prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
		$this->table	= (!empty($this->tbl_prefix)) ? $this->tbl_prefix . $this->table : $this->table;
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

			$sql	= 'CREATE TABLE IF NOT EXISTS `'. $this->table .'` ('
					. '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, '
					. '`name` VARCHAR(32) NOT NULL, '
					. '`backend_access` TINYINT(1) NOT NULL, '
					. '`full_backend_access` TINYINT(1) NOT NULL, '
					. '`status` ENUM(\'enable\', \'disable\', \'deleted\') NOT NULL, '
					. '`is_system` TINYINT(1) NOT NULL DEFAULT 0, '
					. '`added` INT(11) UNSIGNED NOT NULL, '
					. '`modified` INT(11) UNSIGNED NOT NULL, '
					. 'PRIMARY KEY (`id`), '
					. 'KEY `parent_id` (`status`) '
					. ') ENGINE=MYISAM ';
	
			$this->db->query('CREATE',$sql);
		}

        if(!$this->db->query('SELECT', 'SELECT * FROM `'.$this->table.'` LIMIT 0 , 1;')) {
			$insert_data	= TRUE;
		}
        
		if ($insert_data) {
			$sql	= 'INSERT INTO `'. $this->table .'` '
					. '(`id`, `name`, `backend_access`, `full_backend_access`, `status`, `is_system`, `added`, `modified`) '
					. 'VALUES '
					. '(1 , \'super administrator\', \'1\', \'1\', \'enable\', \'1\', '.time().' , 0), '
					. '(2 , \'administrator\', \'1\', \'0\', \'enable\', \'1\', '.time().' , 0), '
					. '(99 , \'user\', \'0\', \'0\', \'enable\', \'1\', '.time().' , 0)';

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
	
	public function load_by_name ($name) {

		$where_cond	= array('name'			=> $name,
							'status !='		=> 'deleted');

		if ($this->find_count($where_cond) == 0)
			unset($where_cond['status !=']);

		$objects		= $this->find($where_cond, '', 1);

		return (isset($objects[0])) ? $objects[0] : FALSE;
	}	


	public function add ($params = '') {
		if (!is_array($params)) return;

		$params['added']	= time();

		unset($this->_model_vars['id']);
		$this->_model_vars['id']		= $this->get_last_id() + 1;
		
		$params	= array_merge($this->_model_vars, $params);
		
		$query = DB::insert($this->tbl_name, array_keys($params))->values(array_values($params))->execute();
		
		if (count($query) !== FALSE) {
			$module_permissions = Model_ModulePermission::instance()->find();
			$params				= array();
			foreach ($module_permissions as $v) {
				$params = array(
							'permission_id'	=> $v->id,
							'level_id'		=> $query[0],
							'value'			=> 0,
							'added'			=> time()
						);
				Model_UserLevelPermission::instance()->add($params);
			}
		} else {
			return FALSE;
		}
		
		return $query[0];
	}
	
	public function get_last_id () {
		$where_cond		= array('id !='		=> 99);
		$order_by		= array('id'		=> 'DESC');
		$last_id		= $this->find($where_cond, $order_by, 1);
		
		if (!isset($last_id[0]))
			return 1;
			
		return $last_id[0]->id;
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
		$result		= $this->db->delete('user_level', $where_cond);

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
			$sql_order = 'ORDER BY '; 
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
			$object			= new Model_UserLevel();

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