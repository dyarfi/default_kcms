<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_UserLevelPermission extends Model_Database {
	protected $table = 'user_level_permissions'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
    protected $tbl_prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'			=> 0,
									'permission_id'	=> 0,
									'level_id'		=> '',
									'value'			=> 0,
									'added'			=> 0,
									'modified'		=> 0);
		
		$this->db		= Database::instance();
		$this->tbl_name	= $this->table;
        $this->tbl_prefix   = $this->db->table_prefix() ? $this->db->table_prefix() : ''; 
		$this->table	= (!empty($this->tbl_prefix)) ? $this->tbl_prefix . $this->table : $this->table;
	}

	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();

		return self::$_instance;
	}

	public function install () {
		$insert_data	= FALSE;

		if (!$this->db->list_tables($this->table)) {
			$insert_data	= TRUE;

			$sql	= 'CREATE TABLE IF NOT EXISTS `'. $this->table .'` ('
					. '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,'
					. '`permission_id` INT(11) NOT NULL,'
					. '`level_id` INT(11) NOT NULL,'
					. '`value` SMALLINT(1) NOT NULL,'
					. '`added` INT(11) NOT NULL,'	
					. '`modified` INT(11) NOT NULL,'	
					. 'INDEX (`id`) '
					. ') ENGINE=MYISAM';
	
			$this->db->query('CREATE',$sql);
			
		}
		
		// Check if table exists
		if(!$this->db->query('SELECT', 'SELECT * FROM `'.$this->table.'` LIMIT 0 , 1;')) {
			// Set insert data to TRUE
			$insert_data	= TRUE;
		}	
		/*
		// Set insert data if TRUE
		if ($insert_data) {
			$sql	= 'INSERT INTO `'.$this->table.'` '
					. '(`id`, `permission_id`, `level_id`, `value`, `added`, `modified`) '
					. 'VALUES '
					. '(NULL, \'superadmin\', \'356a192b7913b04c54574d18c28d46e6395428ab\', \'Super Administrator\', 1, 1, '.time().', \'active\', '.time().', 0), '
					. '(NULL, \'administrator\', \'12506e739378348ec662bb015bfd2288362dcc1c\', \'Administrator\', 2, 1, '.time().', \'active\', '.time().', 0), '
					. '(NULL, \'user@testing.com\', \'12506e739378348ec662bb015bfd2288362dcc1c\', \'User\', 99, 0, '.time().', \'active\', '.time().', 0)';

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

	public function add ($params = '') {
		if (!is_array($params)) return;

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

		$where_cond = array('id' => $id);
		$result		= DB::delete($this->tbl_name)->where(array_keys($where_cond),'=',array_values($where_cond))->execute();
		
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
			$object			= new Model_ModulePermission();

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
	
	public function get_module_function($level_id = '') {
		
		// Check initialize level id
		if($level_id == '') {
			// Return blank array
			return array();
		}	
		
		// Set default loaded modules
		$modules			= array();
		
		// Check admin url
		$where_cond			= array('id'	=> $level_id);
		
		// Set user permissions
		$user_permission	= Model_UserLevel::instance()->find($where_cond, '', 1);

		// Check backend permission
		if(!$user_permission[0]->backend_access) {
			// Set flash alert to session
			$this->session->set('auth_error', 'You have no access');
			// Redirect if have no access to backend / admin-panel
			Request::$current->redirect(ADMIN . 'authentication/noaccess');
		}
		
		// Load user admin menu modules
		$modules['User']		= Lib::config('admin.module_function');
		
		// Check full backend permission
		if ($user_permission[0]->full_backend_access) {
			// Set admin neccesary module functions
			$modules['Module']	= Lib::config('module_list.module_function');
		}
		
		// Set default return
		$return_object	= TRUE;

		// Check for level id
		if ($level_id == '') {
			// Set FALSE if level id not found
			$return_object	= FALSE;
		}
		
		// List all user level permissions
		$user_permissions = $this->find(array('level_id'=>$level_id,'value'=>1));
		// Temp of array
		$buffers = array();
		
		// Loops for user_permission data 
		foreach ($user_permissions as $key) {
			// List all module_permissions based on permission id at user_level_permission
			$module_functions = Model_ModulePermission::instance()->find(array('id'=>$key->permission_id));
			
			// Loops for module_functions data 
			foreach ($module_functions as $val) {
				// List all module_list based on module id at module_permission
				$class_names		= Model_ModuleList::instance()->find(array('id'=>$val->module_id));
	
				// Loops for module_names data 
				foreach($class_names as $module) {				
					// Set temporary data in place
					$buffers[ucwords(str_replace('_', ' ', $module->module_name))][$val->module_link] = $val->module_name;
				}
				
				// Return all computed data of array permissions and module lists available 
				$modules	= array_merge($modules,$buffers);
				
			}
			
		}	
		
		unset($buffers);
		
		return $modules;
	}
	
	public function load_by_name ($name) {
		$where_cond	= array('class_name' => $name);

		$objects	= $this->find($where_cond, '', 1);
		
		return !empty($objects[0]) ? $objects[0] : FALSE;
	}
	
	public function load_by_link ($link) {
		$where_cond	= array('module_link' => $link);

		$objects	= $this->find($where_cond, '', 1);
		
		return !empty($objects[0]) ? $objects[0] : FALSE;
	}
	
	public function load_by_level_id ($level_id) {
		$where_cond	= array('level_id' => $level_id);

		$objects	= $this->find($where_cond, '', 1);
		
		return !empty($objects[0]) ? $objects[0] : FALSE;
	}
	
	public function delete_by_permission_id ($permission_id = '') {
		if ($permission_id == '')
			$permission_id	= $this->permission_id;

		$where_cond	= array('permission_id'	=> $permission_id);
		$result		= DB::delete($this->tbl_name)->where(array_keys($where_cond),'=',array_values($where_cond))->execute();

		return $result;
	}
	
	public function delete_by_level_id ($level_id = '') {
		if ($level_id == '')
			$level_id	= $this->level_id;

		$where_cond	= array('level_id'	=> $level_id);
		$result		= DB::delete($this->tbl_name)->where(array_keys($where_cond),'=',array_values($where_cond))->execute();

		return $result;
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