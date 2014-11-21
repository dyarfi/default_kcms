<?php defined('SYSPATH') OR die('No direct script access.');

class Model_UserDashboard extends Model_Database {
	protected $table = 'user_dashboards'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id' 		=> 0,
									'user_id' 	=> '',
									'level_id' 	=> '',
									'attribute' => '',
									'entity' 	=> '',
									'status' 	=> '',
									'added' 	=> 0,
									'modified' 	=> 0);

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
		$insert_data	= FALSE;
		
		if (!$this->db->list_tables($this->table)) 
               $insert_data	= TRUE;
			   
        $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
            .'`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, '
            .'`user_id` INT NOT NULL , '
            .'`level_id` INT NOT NULL, '
			.'`access` INT(1) NOT NULL DEFAULT \'1\', '
			.'`attribute` TEXT NULL, '
			.'`entity` TEXT NOT NULL, '	
            .'`status` ENUM(\'publish\', \'unpublish\', \'deleted\') NULL DEFAULT \'publish\', '
            .'`added` INT(11) NULL, '
            .'`modified` INT(11) NULL, '
            .'INDEX (`user_id`, `status`) '
            .') ENGINE=MYISAM';

		$this->db->query('CREATE', $sql);
		/*
		if ($insert_data) {
			$sql	= 'INSERT INTO `'.$this->table.'` (`id`,`name`,`position`,`biography`,`quote`,`status`,`added`,`modified`)'
				    . ' VALUES '
				    . '(\'11\',\'Lorem Ipsum Dolor Sit Amet\',\'Type Setting Industry\',\'Lorem Ipsum Dolor Sit Amet\',\'Lorem Ipsum Dolor Sit Amet\',\'publish\',NULL,NULL);';

			$this->db->query('INSERT',$sql);
		}
		*/
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

		$where_cond	= array('id'	=> $id);
		$result		= $this->db->delete($this->table, $where_cond);

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
			$sql_limit = 'LIMIT '. $limit . $offset; 
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
			$object			= new Model_Dashboard();

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
	
	/*** Custom method --- start --- ***/
	public function find_top_count($items='') {
		
		// Check if items is array or not
		if(!is_array($items)) {
			throw new Kohana_Exception('No data available in :name',
				array(':name' => $name));
		}
		
		// Check if table hava a prefix or not and return table name
		$items['table'] = ($this->prefix) ? $this->prefix . $items['table'] : $items['table'];
		
		// Checking if table is exists 
		if (!$this->db->list_tables($items['table'])) {
			throw new Kohana_Exception('There is no table :name available',
				array(':name' => $items['table']));
		}
		
		// Table name
		$table			= $items['table'];
		// Default row limit
		$default_limit	= 'LIMIT 0, 10';
		
		// Check table fields
		$sql_fields = '';
		if (!empty($items['fields'])) {
			$i 	   = 1;
			foreach ($items['fields'] as $order) {
				$split = ($i > 1) ? ', ' : ''; 
				$sql_fields .= ''. $split . $order.'';
				$i++;
			}
			$fields  = $sql_fields;
		} 
		
		// Check table order by
		$sql_order  = ''; 
		$order_by	= '';
		if (!empty($items['order_by'])) {
			$sql_order = ' ORDER BY '; 
			$i 	   = 1;
			foreach ($items['order_by'] as $order => $val) {
				$split = ($i > 1) ? ', ' : ''; 
				$sql_order .= ''. $split .'`'. $order.'` '.$val.'';
				$i++;
			}
			$order_by  = $sql_order;
		} 

		// Check table group by
		$sql_group	= ''; 
		$group_by	= '';
		if (!empty($items['group_by'])) {
			$sql_group = 'GROUP BY '. implode(', ', $items['group_by']); 
		} 
		$group_by = $sql_group;
				
		// Check table row limit
		$sql_limit	= ''; 
		$limit		= '';
		if (!empty($items['limit'])) {
			if (count($items['limit']) != 2) {
				$sql_limit = $default_limit; 
			} else {
				$sql_limit = ' LIMIT '. implode(', ', $items['limit']); 
			}
		} 
		$limit = $sql_limit;
		
		//echo $fields,$group_by,$order_by,$limit;
		$sql =  'SELECT '.$fields.' FROM `'.$table.'` '.$group_by . $order_by . $limit;
				
		$rows = $this->db->query(Database::SELECT, $sql, TRUE)->as_array();

		return $rows;
	}
	/*** Custom method --- end --- ***/

}
