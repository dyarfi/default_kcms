<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Database {
	protected $table = 'users'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $prefix;
	protected static $_instance;
        
	public function __construct() {
		parent::__construct();

		$this->_model_vars	= array('id'		 => 0,
									'email'		 => '',
									'name'		 => '',
									'level_id'	 => 0,
									'is_system'	 => 0,
									'last_login' => 0,
									'status'	 => '',
									'added'		 => 0,
									'modified'	 => 0);

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
                
                $sql            = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ('
                                    . '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,'
                                    . '`email` VARCHAR(255) NOT NULL, '
                                    . '`password` VARCHAR(100) NOT NULL, '
                                    . '`name` VARCHAR(160) NOT NULL, '
                                    . '`level_id` INT(11) UNSIGNED NOT NULL, '
                                    . '`is_system` TINYINT(3) NOT NULL DEFAULT 0, '
						            . '`last_login` INT(11) UNSIGNED NOT NULL, '
                                    . '`status` ENUM(\'verified\', \'active\', \'inactive\', \'blocked\') NOT NULL, '
                                    . '`added` INT(11) UNSIGNED NOT NULL, '
                                    . '`modified` INT(11) UNSIGNED NOT NULL, '
                                    . 'INDEX (`email`, `level_id`) '
                                    . ') ENGINE=MYISAM';

		$this->db->query('CREATE', $sql);
		
        if(!$this->db->query('SELECT', 'SELECT * FROM `'.$this->table.'` LIMIT 0 , 1;'))
			$insert_data	= TRUE;
		
		if ($insert_data) {
			$sql	= 'INSERT INTO `'.$this->table.'` '
					. '(`id`, `email`, `password`, `name`, `level_id`, `is_system`, `last_login`, `status`, `added`, `modified`) '
					. 'VALUES '
					. '(NULL, \'superadmin\', \'356a192b7913b04c54574d18c28d46e6395428ab\', \'Super Administrator\', 1, 1, '.time().', \'active\', '.time().', 0), '
					. '(NULL, \'administrator\', \'12506e739378348ec662bb015bfd2288362dcc1c\', \'Administrator\', 2, 1, '.time().', \'active\', '.time().', 0), '
					. '(NULL, \'user@testing.com\', \'12506e739378348ec662bb015bfd2288362dcc1c\', \'User\', 99, 0, '.time().', \'active\', '.time().', 0)';

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

	public function load_by_email ($email = '') {
		if ($email == '')
			$email	= $this->email;

		$where_cond	= array('email'	=> $email);
		$users		= $this->find($where_cond);

		return (isset($users[0])) ? $users[0] : FALSE;
	}

	public function load_permission () {
		if ($this->level_id == 0) return FALSE;

		$permission	= new Model_UserLevelPermission;

		return $permission->load_by_level_id($this->level_id);
	}
	
	public function load_password ($id = '') {
		if ($id == '') {
			$return_object	= FALSE;
			$id				= $this->id;
		}

		$sql		= 'SELECT password FROM `'.$this->table.'` WHERE `id` = '.$id.'  LIMIT 0,1;';
		$password	= $this->query($sql);
		
		if (!empty($password[0]))
			return $password[0]->password;
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
		
		if ($id == 1)
			return;
			
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
			$object			= new Model_User();

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

	public function authenticate ($email, $password, $level = '') {
		$where_cond	= array('email'		=> $email,
							'password'	=> $password);

		if ($level != '') $where_cond['level_id']	= $level;

		$users		= $this->find($where_cond, '', 1);
        //print_r($users); exit();
		if (!isset($users[0]))
			return FALSE;
		else if ($users[0]->status != 'active')
			return $users[0]->status;

		return $users[0]->id;
	}
	
	public function authenticate_user ($email, $password, $level = '') {
		$where_cond	= array('email'		=> $email,
							'password'	=> $password);

		if ($level != '') $where_cond['level_id']	= $level;

		$users		= $this->find($where_cond, '', 1);
        
		if (!isset($users[0])) {
			return FALSE;
		} else if ($users[0]->status != 'active') {
			return $users[0]->status;
		} /*else if ($users[0]->status != 'verified') {
			return $users[0]->status;
		}
		*/
		return $users[0]->id;
	}

	public function get_user_level ($id) {
		$where_cond	= array('id'		=> $id);
		
		$users		= $this->find($where_cond, '', 1);

		if (!isset($users[0]))
			return FALSE;
		else if ($users[0]->status == 'deleted' || $users[0]->status == 'disable')
			return $users[0]->status;

		return $users[0]->level_id;
	}

	public function change_password ($old_password, $new_password) {
	
		$object_vars = array('password'=>sha1($new_password));
		
		$result = DB::update($this->tbl_name)
					->set($object_vars)
					->where('id', '=', $this->id)
					->and_where('password', '=', sha1($old_password))
					->execute();
		
		return (count($result) == 1);
	}

	public function reset_password ($new_password) {
		
		$object_vars = array('password'=>sha1($new_password));
		
		$result = DB::update($this->tbl_name)->set($object_vars)->where('id', '=', $this->id)->execute();
		
		return (count($result) == 1);
	}
	
	public function query ($query = '') {
		if($query == '')
			return FALSE;
		
		$query = $this->db->query(Database::SELECT, $query, TRUE);
		
		if($query !== FALSE)
			return $query;
		else
			return FALSE;	
	}	
}
