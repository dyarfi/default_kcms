<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Configuration extends Model_Database {
	private $table = 'configurations'; 
	private $tbl_name;
	private $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('parameter'		=> '',
									'value'			=> 0);

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
					. '`parameter` VARCHAR(150) NOT NULL DEFAULT \'\', '
					. '`value` VARCHAR(150) NOT NULL DEFAULT \'\', '
					. 'PRIMARY KEY (`parameter`, `value`) '
					. ') ENGINE=MYISAM';

			$this->db->query('CREATE',$sql);
		}
		
		if ($insert_data) {
			$sql	= 'INSERT INTO `'.$this->table.'` '
					. '(`parameter`, `value`) '
					. 'VALUES '
					. '(\'install\', \'0\'),'
					. '(\'maintenance\', \'0\'),'
					. '(\'environment\', \'0\'),'
					. '(\'theme\', \'0\')';

			$this->db->query('INSERT',$sql);
		}

		return $this->db->list_tables($this->table);
	}

	public function load ($parameter = '') {
		$return_object	= TRUE;

		if ($parameter == '') {
			$return_object	= FALSE;
			$id				= $this->value;
		}

		$objects	= $this->find(array('parameter' => $parameter), '', 1);

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

		$params	= array_merge($this->_model_vars, $params);
		
		$query = DB::insert($this->tbl_name, array_keys($params))->values(array_values($params))->execute();

		if (count($query) !== FALSE)
			$insert_id	= mysql_insert_id();
		else
			return FALSE;

		return $insert_id;
	}

	public function update () {
		$object_vars	= get_object_vars($this);
		
		unset($object_vars['_model_vars'], $object_vars['db']);
		
		$object_vars = Arr::overwrite($this->_model_vars,$object_vars);
		
		$result = DB::update($this->tbl_name)->set($object_vars)->where('parameter', '=', $this->parameter)->execute();
		return $result;
	}

	public function delete ($parameter = '') {
		if ($parameter == '')
			$parameter	= $this->parameter;

		$where_cond	= array('parameter'	=> $parameter);
		$result		= $this->db->delete('configuration', $where_cond);
		
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
			$object			= new Model_Configuration;

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
	
	// Method for get environment variable from database
	public function get_environment() {		
		// Return result for environment setting that will be catch into $_SERVER['KOHANA_ENV']
		return @$this->load('environment')->value;
	}
		
	// Method for set environment variable from database "Production || Development"
	public function set_environment() {
		
	}
	
}
