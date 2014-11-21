<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Language extends Model_Database {

	protected $table = 'languages'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $_prefix;
	protected static $_instance;
	
	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'				=> 0,
									'name'				=> '',
									'prefix'			=> '',
									'default'			=> 0,
									'file_name'			=> '',
									'status'			=> '',
									'is_system'			=> '',
									'added'				=> 0,
									'modified'			=> 0);

		$this->db		= Database::instance();
		$this->tbl_name = $this->table;
		$this->_prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
        $this->table	= (!empty($this->_prefix)) ? $this->_prefix . $this->table : $this->table;
	}

	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();

		return self::$_instance;
	}

	public function install () {
		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
				. '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, '
				. '`name` VARCHAR(128) NULL, '
				. '`prefix` VARCHAR(10) NULL, '
				. '`default` TINYINT(1) NULL, '
				. '`file_name` VARCHAR(128) NULL, '
				. '`status` TINYINT(1) NULL DEFAULT 1, '
				. '`is_system` TINYINT(1) NULL DEFAULT 0, '
				. '`added` INT(11) UNSIGNED NULL, '
				. '`modified` INT(11) UNSIGNED NULL '
				. ') ENGINE=MYISAM';

		$this->db->query('CREATE', $sql);
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

	public function load_by_name ($name, $category_id = '') {
		if ($category_id != '') {
			$where_cond	= array('name'			=> $name,
								'category_id'	=> $category_id,
								'status !='		=> 'deleted');
		} else {
			$where_cond	= array('name'			=> $name,
								'status !='		=> 'deleted');
		}
		if ($this->find_count($where_cond) == 0)
			unset($where_cond['status !=']);
		$objects		= $this->find($where_cond, '', 1);
		return (isset($objects[0])) ? $objects[0] : FALSE;
	}
	
	public function load_by_prefix ($tbl_prefix) {
		if ($tbl_prefix != '') {
			$where_cond	= array('prefix'		=> $tbl_prefix,
								'status !='		=> 0);
		}
		
		if ($this->find_count($where_cond) == 0)
			unset($where_cond['status !=']);
		
		$objects		= $this->find($where_cond, '', 1);
		return (isset($objects[0])) ? $objects[0] : FALSE;
	}
	
	public function add ($params = '') {
		if (!is_array($params)) return;

		$params['added']	= mktime();

		unset($this->model_vars['id']);

		$params	= array_merge($this->_model_vars, $params);
		$query	= $this->db->insert('language', $params);

		if (count($query) !== FALSE)
			$insert_id	= $query->insert_id();
		else
			return FALSE;

		$this->db->clear_cache(TRUE);

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
		
		$result		= DB::delete($this->tbl_name)->where('id', '=', $id)->execute();
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
			$sql_order = ' ORDER BY'; 
			$i 	   = 1;
			foreach ($order_by as $order => $val) {
				$split = ($i > 1) ? ', ' : ''; 
				$sql_order .= ''. $split .' `'. $order.'` '.$val.' ';
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
			$object			= new Model_Language;
			$object_vars	= get_object_vars($row);
			foreach ($object_vars as $var => $val) {
				$object->$var	= $val;
			}
			if ($limit == 1)
				$returns = (object) $object_vars;
			else
				$returns[]	= $object;
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
