<?php defined('SYSPATH') or die('No direct script access.');

 class Model_CareerDivision extends Model_Database {
	protected $table = 'career_divisions'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array(
									'id'		=> '',
									'name'			=> '',
									'subject'		=> '',
									'description'	=> '',
									'is_system' => '',
									'status'	=> '',
									'added'		=> '',
									'modified'	=> '');

		$this->db		= Database::instance();
		$this->tbl_name	= $this->table;
		$this->prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
		$this->table	= (!empty($this->prefix)) ? $this->prefix .$this->table: $this->table;
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

		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` VARCHAR(255) NULL, 
				  `subject` VARCHAR(255) NULL, 
				  `description` text NOT NULL,
				  `is_system` tinyint(1) NOT NULL DEFAULT \'1\',
				  `status` ENUM(\'publish\', \'unpublish\', \'deleted\') NULL DEFAULT \'publish\', 
				  `added` int(11) NOT NULL,
				  `modified` int(11) NOT NULL, 
				  INDEX (`name`, `status`), 
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11;';
				
		$this->db->query('CREATE', $sql);

		if ($insert_data) {
			$sql	 = 'INSERT INTO `'.$this->table.'` (`id`, `name`, `subject`, `description`, `is_system`, `status`, `added`, `modified`)'
					. 'VALUES (12, \'human-resources\', \'Human Resources\', \'Lorem Ipsum Dolor Sit Amet\', 1, \'publish\', '.time().', 0),'
					. '(13, \'creative\', \'Creative\', \'Lorem Ipsum Dolor Sit Amet\', 1, \'publish\', '.time().', 0);';

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
			$object			= new Model_CareerDivision;

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

?>