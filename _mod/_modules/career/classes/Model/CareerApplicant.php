<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CareerApplicant extends Model_Database {
	protected $table = 'career_applicants'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'				=> 0,
									'career_id'			=> 0,
									'name'				=> '',
									'email'				=> '',
									'gender'			=> '',
									'marital_status'	=> '',
									'id_number'			=> '',
									'phone'				=> '',
									'address'			=> '',
									'birth_date'		=> '',
									'birth_place'		=> '',
									'education_grade'	=> '',
									'education_name'	=> '',
									'education_major'	=> '',
									'education_from'	=> 0,
									'education_to'		=> 0,
									'employment_name'	=> '',
									'employment_position' => '',
									'employment_from'	=> 0,
									'employment_to'		=> 0,
									'photo'				=> '',
									'cv_file'			=> '',
									'is_located'		=> 0,
									'is_related'		=> 0,
									'messages'			=> '',
									'available_date'	=> '',
									'expected_salary'	=> '',
									'status'			=> '',
									'added'				=> 0,
									'modified'			=> 0);

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
		
		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
				.'`id` int(11) NOT NULL AUTO_INCREMENT, '
				.'`career_id` int(11) NOT NULL, '
				.'`name` varchar(128) NOT NULL, '
				.'`email` varchar(64) NOT NULL, '
				.'`gender` tinyint(1) NOT NULL, '
				.'`marital_status` tinyint(1) NOT NULL, '
				.'`id_number` varchar(128) NOT NULL, '
				.'`phone` varchar(18) NOT NULL, '
				.'`address` varchar(512) NOT NULL, '
				.'`birth_date` int(11) NOT NULL, '
				.'`birth_place` char(32) NOT NULL, '
				.'`education_grade` varchar(128) NOT NULL, '
				.'`education_name` varchar(128) NOT NULL, '
				.'`education_major` varchar(128) NOT NULL, '
				.'`education_from` int(11) NOT NULL, '
				.'`education_to` int(11) NOT NULL, '
				.'`employment_name` int(11) NOT NULL, '
				.'`employment_position` int(11) NOT NULL, '
				.'`employment_from` int(11) NOT NULL, '
				.'`employment_to` int(11) NOT NULL, '
				.'`photo` varchar(256) NOT NULL, '
				.'`cv_file` varchar(256) NOT NULL, '
				.'`is_located` tinyint(1) NOT NULL, '
				.'`is_related` tinyint(1) NOT NULL, '
				.'`messages` TEXT NULL, '
				.'`available_date` int(11) NOT NULL, '
				.'`expected_salary` int(11) NOT NULL, '
				.'`status` ENUM(\'publish\', \'unpublish\', \'deleted\') NULL DEFAULT \'publish\', '
				.'`added` int(11) NOT NULL, '
				.'`modified` int(11) NOT NULL, '
				.'PRIMARY KEY (`id`) '
				//.'UNIQUE KEY `name` (`name`) '
				.') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
			
		$this->db->query('INSERT',$sql);
		
		return $this->db->list_tables($this->table);

	}

	public function load ($id = '') {
		$return_object	= TRUE;

		if ($id == '') {
			$return_object	= FALSE;
			$id				= $this->id;
		}
		$where_cond = array('id'=>$id);
		$objects	= $this->find($where_cond, '', 1);

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

		$result		= $this->db->query('DELETE', 'DELETE from `'.$this->table.'` WHERE id ='.$id.';');

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
			$object			= new Model_CareerApplicant;

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