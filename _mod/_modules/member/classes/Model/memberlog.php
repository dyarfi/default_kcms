<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_MemberLog extends Model {
	
	protected $table = 'member_logs'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;
	
	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('user_id'				=> 0,
									'content'				=> '',
									'status'				=> 'inactive',
									'added'					=> 0,
									'modified'				=> 0);

		$this->db			= Database::instance();
	}

	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();

		return self::$_instance;
	}

	public function install () {
		$insert_data		= FALSE;

		if (!$this->db->table_exists('user_log'))
			$insert_data	= TRUE;

		$sql	= 'CREATE TABLE IF NOT EXISTS `user_log` ('
				. '`user_id` INT(11) UNSIGNED NOT NULL,'
				. '`content` TEXT NULL, '
				. '`status` ENUM(\'active\', \'inactive\', \'deleted\') NOT NULL DEFAULT \'active\', '
				. '`added` INT(11) UNSIGNED NOT NULL, '
				. '`modified` INT(11) UNSIGNED NOT NULL, '
				. 'INDEX (`user_id`) '
				. ') ENGINE=MYISAM';

		$this->db->query($sql);

		return $this->db->table_exists('user_log');
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

	public function load_by_name ($name = '') {
		if ($name == '')
			$name	= $this->name;

		$where_cond	= array('name'	=> $name);
		$name		= $this->find($where_cond);

		return (isset($name[0])) ? $name[0] : FALSE;
	}

	public function add ($params = '') {
		if (!is_array($params)) return;

		$params['added']	= mktime();

		unset($this->model_vars['user_id']);

		$params	= array_merge($this->_model_vars, $params);
		$query	= $this->db->insert('user_log', $params);

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

		unset($object_vars['user_id'], $object_vars['_model_vars'], $object_vars['db']);

		$where_cond		= array('user_id'	=> $this->user_id);

		$result			= $this->db->update('user_log', $object_vars, $where_cond);

		$this->db->clear_cache(TRUE);

		return $result;
	}

	public function delete ($user_id = '') {
		if ($user_id == '')
			$user_id	= $this->id;

		$where_cond	= array('user_id'	=> $user_id);
		$result		= $this->db->delete('user_log', $where_cond);

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

		if ($order_by != '')
			$this->db->orderby($order_by);

		if ($limit != '' && $offset != '')
			$this->db->limit($limit, $offset);
		else if ($limit != '')
			$this->db->limit($limit);

		if ($where_cond != '')
			$rows	= $this->db->where($where_cond)->get('user_log');
		else
			$rows	= $this->db->get('user_log');

		$returns	= array();

		foreach ($rows as $row) {
			$object			= new MY_Member();

			$object_vars	= get_object_vars($row);

			unset($object_vars['_model_vars'], $object_vars['db']);

			foreach ($object_vars as $var => $val) {
				$object->$var	= $val;
			}

			$returns[]		= $object;

			unset($object, $vars);
		}
		//print_r($this->db->last_query());
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

		return $this->db->count_records('user_log', $where_cond);
	}
                
}

