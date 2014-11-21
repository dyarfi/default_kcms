<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Page extends Model_Database {
    protected $table = 'pages'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;
	
	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'				=> 0,
									'category_id'		=> 0,
									'name'				=> '',
									'subject'			=> '',
									'synopsis'			=> '',
									'text'				=> '',
									'attribute'			=> '',
									'publish_date'		=> '0000-00-00',
									'unpublish_date'	=> '0000-00-00',
									'allow_comment'		=> 0,
									'tags'				=> '',
									'order'				=> 0,
									'user_id'			=> 0,
									'count'				=> 0,
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
		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
				. '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, '
				. '`category_id` INT(11) UNSIGNED NULL, '
				. '`name` VARCHAR(255) NULL, '
				. '`subject` VARCHAR(255) NULL, '
				. '`synopsis` TEXT NULL, '
				. '`text` TEXT NULL, '
				. '`attribute` TEXT NULL, '
				. '`publish_date` DATE NULL, '
				. '`unpublish_date` DATE NULL, '
				. '`allow_comment` TINYINT(1) NULL, '
				. '`tags` TEXT NULL, '
				. '`order` TINYINT(3) NULL, '
				. '`user_id` TINYINT(3) NULL , '
				. '`count` INT(11) NULL , '	
				. '`status` ENUM(\'publish\', \'unpublish\', \'draft\', \'deleted\') NULL DEFAULT \'draft\', '
				. '`added` INT(11) NULL, '
				. '`modified` INT(11) NULL, '
				. 'INDEX (`category_id`, `name`, `publish_date`, `unpublish_date`, `allow_comment`, `order`, `status`) '
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

	public function load_published ($params = '') {
		if (!is_array($params))
			$params	= array();

		if (!isset($params['start']))
			$params['start']	= date('Y-m-d');

		if (!isset($params['end']))
			$params['end']		= '0000-00-00';

		if (!isset($params['where_cond']))
			$params['where_cond']	= array();

		if (!isset($params['order_by']))
			$params['order_by']	= '';

		if (!isset($params['limit']))
			$params['limit']	= '';

		if (!isset($params['offset']))
			$params['offset']	= '';

		$where_cond	= array('publish_date <='	=> $params['start'],
							'unpublish_date >='	=> $params['end']);

		$where_cond	= array_merge($where_cond, $params['where_cond']);

		return $this->find($where_cond, $params['order_by'], $params['limit'], $params['offset']);
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
		$result		= $this->db->delete('page', $where_cond);

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
			$object			= new Model_Page();

			$object_vars	= get_object_vars($row);

			foreach ($object_vars as $var => $val) {
				$object->$var	= $val;
			}

			//if (count($rows) == 1)
				//$returns = (object) $object_vars;
			//else
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
