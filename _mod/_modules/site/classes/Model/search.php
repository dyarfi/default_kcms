<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search extends Model_Database {
	protected $table = 'search'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'				=> 0,
									'title'				=> '',
									'subject'			=> '',
									'content'			=> '',
									'url'				=> '',
									'session'			=> '',
									'code'				=> '');

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

		$params['added']	= mktime();

		unset($this->model_vars['id']);

		$params	= array_merge($this->_model_vars, $params);
		$query	= $this->db->insert('search', $params);

		if (count($query) !== FALSE)
			$insert_id	= $query->insert_id();
		else
			return FALSE;

		$this->db->clear_cache(TRUE);

		return $insert_id;
	}

	public function update () {
		$this->modified	= mktime();
		$object_vars	= get_object_vars($this);

		unset($object_vars['id'], $object_vars['_model_vars'], $object_vars['db']);

		$where_cond		= array('id'	=> $this->id);

		$result			= $this->db->update('search', $object_vars, $where_cond);

		$this->db->clear_cache(TRUE);

		return $result;
	}

	public function delete ($id = '') {
		if ($id == '')
			$id	= $this->id;

		$where_cond	= array('id'	=> $id);
		$result		= $this->db->delete('search', $where_cond);

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
			$object			= new Model_Search();

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
		}

		return $this->db->count_records('search', $where_cond);
	}
	
	public function query ($query = '') {
		if($query == '')
			return FALSE;
		
		$query = $this->db->query(Database::SELECT,$query);
		
		if($query !== FALSE)
			return $query;
		else
			return FALSE;	
	}
}
