<?php defined('SYSPATH') OR die('No direct access allowed.');class Model_DownloadFile extends Model_Database {    protected $table = 'download_files'; 	protected $tbl_name;	protected $_model_vars;	protected $db;     protected $prefix;	protected static $_instance;		public function __construct () {		parent::__construct();		$this->_model_vars	= array('id'			=> 0,									'type_id'		=> 0,									'category_id'	=> 0,									'product_id'	=> 0,																		'name'			=> '',									'title'			=> '',									'description'	=> '',									'field_name'	=> '',									'file_name'		=> '',									'file_type'		=> '',									'caption'		=> '',									'allow_comment'	=> 0,									'order'			=> 0,									'user_id'		=> 0,									'count'			=> 0,										'status'		=> '',									'added'			=> 0,									'modified'		=> 0);		$this->db		= Database::instance();		$this->tbl_name = $this->table;		$this->prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : '';         $this->table	= (!empty($this->prefix)) ? $this->prefix . $this->table : $this->table;	}		public static function instance () {		if (self::$_instance === NULL)			self::$_instance	= new self();		return self::$_instance;	}		public function install () {		$sql	= 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('				. '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, '				. '`type_id` INT(11) UNSIGNED NOT NULL, '				. '`category_id` INT(11) UNSIGNED NOT NULL, '				. '`product_id` INT(11) UNSIGNED NOT NULL, '				. '`name` VARCHAR(96) NOT NULL, '				. '`title` VARCHAR(255) NOT NULL, '				. '`description` TEXT NOT NULL, '				. '`field_name` VARCHAR(128) NOT NULL, '				. '`file_name` VARCHAR(96) NOT NULL, '				. '`file_type` VARCHAR(255) NOT NULL, '				. '`caption` VARCHAR(255) NOT NULL, '				. '`allow_comment` TINYINT(1) NOT NULL, '				. '`user_id` TINYINT(3) NULL , '				. '`count` INT(11) NULL , '				. '`order` TINYINT(5) NULL, '					. '`status` ENUM(\'publish\', \'unpublish\', \'deleted\') NOT NULL DEFAULT \'unpublish\', '				. '`added` INT(11) UNSIGNED NOT NULL, '				. '`modified` INT(11) UNSIGNED NOT NULL, '				. 'INDEX (`type_id`, `name`, `allow_comment`, `status`) '				. ') ENGINE=MYISAM';		$this->db->query('CREATE', $sql);		return $this->db->list_tables($this->table);	}		public function load ($id = '') {		$return_object	= TRUE;		if ($id == '') {			$return_object	= FALSE;			$id				= $this->id;		}		$objects	= $this->find(array('id' => $id), '', 1);		if (count($objects) == 1) {			if ($return_object) {				return $objects[0];			} else {				$vars	= array_keys($this->_model_vars);				foreach ($vars as $var) {					$this->$var	= $objects[0]->$var;				}				return TRUE;			}		}		return FALSE;	}		public function load_by_filename ($file_name, $type_id = '') {		if ($type_id != '') {			$where_cond	= array('file_name'		=> $file_name,								'type_id'		=> $type_id,								'status !='		=> 'deleted');		} else {			$where_cond	= array('file_name'		=> $file_name,								'status !='		=> 'deleted');		}		if ($this->find_count($where_cond) == 0)			unset($where_cond['status !=']);		$objects		= $this->find($where_cond, '', 1);		return (isset($objects[0])) ? $objects[0] : FALSE;	}		public function load_by_type_id ($type_id = '') {		$order_by = array('added'=>'desc');		if ($type_id != '') {			$where_cond	= array('type_id'		=> $type_id,								'status !='		=> 'deleted');		} else {			$where_cond	= array('status !='		=> 'deleted');		}		if ($this->find_count($where_cond) == 0)			unset($where_cond['status !=']);		$objects		= $this->find($where_cond, $order_by,'');		return (isset($objects)) ? $objects : FALSE;	}			public function last_order($type_id = '', $group = '') {		$type = '';		if (!empty($type_id)) {			$type = 'WHERE `type_id` = '. $type_id .'';		} 		// query to find default data 		$sql = 'SELECT MAX(`order`) as `max_order` FROM `'.$this->table.'` '.$type.';';		$max_order = $this->manual_query($sql,TRUE);		if (!empty($max_order[0]->max_order))			return $max_order[0]->max_order;	}		public function first_order($type_id = '') {		$type = '';		if (!empty($type_id)) {			$type = 'WHERE `type_id` = '. $type_id .'';		} 		// query to find default data 		$sql = 'SELECT MIN(`order`) as `min_order` FROM `'.$this->table.'` '.$type.';';		$min_order = $this->manual_query($sql,TRUE);		if (!empty($min_order[0]->min_order))			return $min_order[0]->min_order;	}		public function add ($params = '') {		if (!is_array($params)) return;		$params['added']	= time();		unset($this->_model_vars['id']);		$params	= array_merge($this->_model_vars, $params);		$query = DB::insert($this->tbl_name, array_keys($params))->values(array_values($params))->execute();		if (count($query) !== FALSE)			$insert_id	= mysql_insert_id();		else			return FALSE;		return $insert_id;	}		public function update () {		$this->modified	= time();		$object_vars	= get_object_vars($this);		unset($object_vars['_model_vars'], $object_vars['db']);		$object_vars = Arr::overwrite($this->_model_vars,$object_vars);		$result = DB::update($this->tbl_name)->set($object_vars)->where('id', '=', $this->id)->execute();		return $result;	}		public function delete ($id = '') {		if ($id == '')			$id	= $this->id;		$result		= DB::delete($this->tbl_name)->where('id', '=', $id)->execute();		return $result;	}		public function find ($where_cond = '', $order_by = '', $limit = '', $offset = '') {		/** Build where query **/		if ($where_cond != '') {			if (is_array($where_cond) && count($where_cond) != 0) {				$buffers	= array();				$operators	= array('LIKE',									'IN',									'!=',									'>=',									'<=',									'>',									'<',									'=');				foreach ($where_cond as $field => $value) {					$operator	= '=';					if (strpos($field, ' ') != 0)						list($field, $operator)	= explode(' ', $field);					if (in_array($operator, $operators)) {						$field		= '`'.$field.'`';						if ($operator == 'IN' && is_array($value))							$buffers[]	= $field.' '.$operator.' (\''.implode('\', \'', $value).'\')';						else							$buffers[]	= $field.' '.$operator.' \''.$value.'\'';					} else if (is_numeric($field)) {						$buffers[]	= $value;					} else {						$buffers[]	= $field.' '.$operator.' \''.$value.'\'';					}				}                				$where_cond	= implode(' AND ', $buffers);                   			}		}		$sql_order = ''; 		if ($order_by != '') {			$sql_order = ' ORDER BY'; 			$i 	   = 1;			foreach ($order_by as $order => $val) {				$split = ($i > 1) ? ', ' : ''; 				$sql_order .= ''. $split .' `'. $order.'` '.$val.' ';				$i++;			}			$order_by  = $sql_order;		}		$sql_limit = ''; 		if ($limit != '' && $offset != '') {			$offset    = $offset . ','; 			$sql_limit = 'LIMIT '. $offset . $limit; 		}		else if ($limit != '') {			$sql_limit = 'LIMIT '. $limit; 		}		$limit = $sql_limit;		if ($where_cond != '') {			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'` WHERE '. $where_cond . $order_by . $limit, TRUE)->as_array();		}		else {			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'`' . $order_by . $limit, TRUE)->as_array();		}		$returns	= array();		foreach ($rows as $row) {			$object			= new Model_DownloadFile;			$object_vars	= get_object_vars($row);			unset($object_vars['_model_vars'], $object_vars['db']);			foreach ($object_vars as $var => $val) {				$object->$var	= $val;			}			$returns[]		= $object;			unset($object, $vars);		}		return $returns;	}		public function find_count ($where_cond = '') {		/** Build where query **/		if ($where_cond != '') {			if (is_array($where_cond) && count($where_cond) != 0) {				$buffers	= array();				$operators	= array('LIKE',									'IN',									'!=',									'>=',									'<=',									'>',									'<',									'=');				foreach ($where_cond as $field => $value) {					$operator	= '=';					if (strpos($field, ' ') != 0)						list($field, $operator)	= explode(' ', $field);					if (in_array($operator, $operators)) {						$field		= '`'.$field.'`';						if ($operator == 'IN' && is_array($value))							$buffers[]	= $field.' '.$operator.' (\''.implode('\', \'', $value).'\')';						else							$buffers[]	= $field.' '.$operator.' \''.$value.'\'';					} else if (is_numeric($field)) {						$buffers[]	= $value;					} else {						$buffers[]	= $field.' '.$operator.' \''.$value.'\'';					}				}				$where_cond	= implode(' AND ', $buffers);			}			$rows = $this->db->query(Database::SELECT, 'SELECT * FROM `'.$this->table.'` WHERE '. $where_cond, TRUE)->count();		} else {			$rows = sizeof($this->find());		}		return $rows;	}		public function manual_query ($query = '',$object=FALSE) {		if($query == '')			return FALSE;		$query = $this->db->query(Database::SELECT, $query, $object);		if($query !== FALSE)			return $query;		else			return FALSE;		}}