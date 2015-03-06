<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_ModuleList extends Model_Database {
	protected $table = 'module_lists'; 
	protected $tbl_name;
	protected $_model_vars;
	protected $db; 
	protected $tbl_prefix;
	protected static $_instance;

	public function __construct () {
		parent::__construct();

		$this->_model_vars	= array('id'			=> 0,
									'parent_id'		=> 0,
									'module_name'	=> '',
									'module_link'	=> '',
									'order'			=> 0);
		
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
					. '`parent_id` INT(11) NOT NULL,'
					. '`module_name` VARCHAR(255) NOT NULL, '
					. '`module_link` VARCHAR(255) default NULL, '
					. '`order` INT(11) NOT NULL,'
					. 'INDEX (`id`) '
					. ') ENGINE=MYISAM';
	
			$this->db->query('CREATE',$sql);
		}
		
        return $this->db->list_tables($this->table);
	}
	
	public function module_check () {
		$modules	= array();
		
		// List all custom modules
		foreach (Kohana::modules() as $row) {
			$module_app				= strstr($row, MODPATH . APPMOD);
			$module					= str_replace(MODPATH . APPMOD, '', $module_app);
			$class_name			= str_replace(DS, '', $module);
			$config_module[]		= $class_name;
		}
		
		// Check config module
		if(is_array($config_module) && count($config_module) > 0) {			
							
			// List DB module
			$module_list	= array();
			$where_cond		= array('parent_id'		=> 0);
			$module_db		= $this->find($where_cond);
			
			$user_levels	= Model_UserLevel::instance()->find();
			
			$buffers		= array();
			if(is_array($module_db) && count($module_db) != 0) {
				foreach($module_db as $module) {
					//$buffers[$module->id]	= $module->module_name;
					$buffers[$module->id]	= str_replace(' ', '_', $module->module_name);
				}
				
				$module_list	= $buffers;
				unset($buffers);
			}			
			
			$new_module_perm_idx = '';
			$new_module_perm_fnc = '';
			
			foreach($config_module as $row) {

				// Check Module Revoke Access							
				$revoke	= Lib::config($row.'.revoke');

				if ($revoke) {
					// Find module id
					$revoke_ids = $this->find(array('module_name'=>$row));

					// Check if empty
					if (!empty($revoke_ids) && is_array($revoke_ids)) {

						// Loop through ids
						foreach ($revoke_ids as $ids) {		

							//print_r($ids);
							//exit;
							// Find module_permission with module_id
							$permission_id = Model_ModulePermission::instance()->find(array('module_id'=>$ids->id));

							// Looping through permission
							foreach ($permission_id as $perm_id) {								
								// Deleted User Permission
								Model_UserLevelPermission::instance()->delete_by_permission_id($perm_id->id);
							}		

							// Deleted Permission
							Model_ModulePermission::instance()->delete_by_module_id($ids->id);

							// Deleted Module					
							$this->delete($ids->id);

							// Deleted Child Module					
							$this->delete_by_parent_id($ids->id);
						}
					} 
				}

				// Mark for main site modules to not included
				if($row	!= 'site' && !empty($row)) {
					
					// Check new module and install it	
					if(!in_array($row, $module_list)) {
						if (is_file(MODPATH . APPMOD . DS . $row . DS .'config/' .$row. '.php')) {	
														
							// Module install
							$models	= Lib::config($row.'.models');
							if (!is_array($models)) {
								continue;
							}
							// Insert modules to db
							$where_cond			= array('parent_id'		=> 0);
							$order_by			= array('order'			=> 'DESC');
							$module_last_order	= $this->find($where_cond, $order_by, 1);

							$i	= (isset($module_last_order[0])) ? $module_last_order[0]->order + 1 : 0;
							
							$params		= array('parent_id'		=> 0,
												'module_name'	=> str_replace('_', ' ', $row),
												'module_link'	=> '#',
												'order'			=> $i);
							
							$module_id	= $this->add($params);

							foreach ($models as $model) {
								$object_name	= 'Model_' . implode('_', array_map('ucfirst', explode('_', $model)));
								$object			= new $object_name;
								
								// Check if install method is exists
								if (method_exists($object, 'install')) {
								
									$object->install();

									// Add to modules to model list
									$params				= array('module_id'		=> $module_id,
																'model'			=> $object_name);
									
									$new_module_list[]	= Model_ModelList::instance()->add($params);
								}

								unset($object, $params);
							}

							// Module Menu Check
							$module_menu	= Lib::config($row.'.module_menu');
							
							// Params Permissions
							// $params_perm	= array();
							
							if(is_array($module_menu)) {
								$menu_order		= 0;
								foreach($module_menu as $menu => $menu_name) {
									$params		= array(
														'parent_id'		=> $module_id,
														'module_name'	=> $menu_name,
														'module_link'	=> $menu,
														'order'			=> $menu_order);

									$new_module_function[] = $this->add($params);
									//$new_module_function[] = $params1;
									
									// Set module_id var
									$params['module_id'] = $params['parent_id'];
									
									// Unset parent_id var
									unset($params['parent_id']);
									
									// Adding initial controller index to module permission
									$new_module_perm_idx[] = Model_ModulePermission::instance()->add($params);

									$menu_order++;
								}
								unset($params);
							}
							
							// Module Function Check
							$module_function	= Lib::config($row.'.module_function');
							
							if(is_array($module_function)) {
								$function_order	= $menu_order;
								foreach($module_function as $function => $function_name) {
									if (!$this->find(array('module_name' =>$function_name))) {
									$params		= array(
														'module_id'	 	 => $module_id,
														'module_name'	 => $function_name,
														'module_link'	 => $function,
														'order'			 => $function_order);
									
									// Adding action controller to module permission
									$new_module_perm_fnc[] = Model_ModulePermission::instance()->add($params);

									//$new_module_perm_fnc[] = $params;

									$function_order++;
									}
								}
								unset($params);
							}
							
							$i++;
						}
					}
				}
			}
			
			if (!empty($new_module_perm_idx) && !empty($new_module_perm_fnc)) {
				
				$new_module_permission = array_merge($new_module_perm_idx, $new_module_perm_fnc);

				// Check user level permission for a new modules just installed
				if (!empty($new_module_permission) && is_array($new_module_permission)) {
					foreach ($user_levels as $levels) {
						foreach($new_module_permission as $new_permission) {

							if($levels->id == 1 || $levels->id == 2) {
								$value	= 1;
							} else { 
								$value	= 0;
							}

							$params		= array('permission_id'	=> $new_permission,
												'level_id'		=> $levels->id,
												'value'			=> $value,
												'added'			=> time(),
												'modified'		=> 0);

							$user_level_permission = Model_UserLevelPermission::instance()->add($params);
						}
					}
				}

			}
			
			// Check deleted module
			$deleted_module	= array_diff($module_list, $config_module);

			if(count($deleted_module) != 0) {
				foreach($deleted_module as $key	=> $value) {
					if($this->delete($key)) {
						$this->delete_by_parent_id($key);
						$where_cond	= array('module_id'	=> $key);
						$model_list	= Model_ModelList::instance()->find($where_cond);
						if(is_array($model_list) && count($model_list) != 0) {
							foreach($model_list as $model) {
								$model_name	= strtolower(str_replace('_Model','',$model->model));
								if ($this->db->list_tables($model_name)) 
									$this->db->query('DROP TABLE `'.$model_name.'`'); // $this->db->query('DROP TABLE `'.$model_name.'`');	
							}							 
						}

						Model_ModelList::instance()->delete_by_module_id($key);
						Model_ModulePermission::instance()->delete_by_module_id($key);
						Model_UserLevelPermission::instance()->delete_by_permission_id($key);
						if ($this->db->list_tables($value)) 
							$this->db->query('DROP TABLE `'.$value.'`');//$this->db->query('DROP TABLE `'.$value.'`');	
					}
				}
			}
			
		}
		
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

	public function delete_by_parent_id ($parent_id = '') {
		if ($parent_id == '')
			$parent_id	= $this->parent_id;

		$where_cond = array('parent_id' => $parent_id);
		
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
							$buffers[]	= $field.' '.$operator.' (\''. implode('\', \'', $value).'\')';
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
			$sql_limit = ' LIMIT '. $offset . $limit; 
		}
		else if ($limit != '') {
			$sql_limit = ' LIMIT '. $limit; 
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
			$object			= new Model_ModuleList;

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
	
	public function load_by_name ($name) {
		$where_cond	= array('module_name' => $name);

		$objects		= $this->find($where_cond, '', 1);
		
		return !empty($objects[0]) ? $objects[0] : FALSE;
	}
	
	public function load_by_link ($link) {
		$where_cond	= array('module_link' => $link);

		$objects		= $this->find($where_cond, '', 1);
		
		return !empty($objects[0]) ? $objects[0] : FALSE;
	}
	
	public function parent_level_module ($id = '') {
		if ($id == '')
			$id	= $this->id;

		$where_cond	= array('id'	=> $id);
		$module		= $this->find($where_cond, '', 1);

		if (!isset($module[0]))
			return FALSE;

		return $module[0]->parent_id;
		
	}
	
	public function get_modules ($user_level) {
		if($user_level == '')
			return array();
	
		$modules		= array();
	
		// Check admin url
		$where_cond		= array('id'	=> $user_level);
		$user_permission	= Model_UserLevel::instance()->find($where_cond, '', 1);
		
		// Check backend permission
		if(!$user_permission[0]->backend_access) {
		    // Set flash alert to session
		    Session::instance()->set('auth_error', 'You have no access');
		    // Redirect if have no access to backend / admin-panel
		    HTTP::redirect(ADMIN . 'authentication/noaccess');
		}
		
		$modules['User']		= Lib::config('admin.module_menu');
		
		// Check full backend permission
		if ($user_permission[0]->full_backend_access) { 
			$modules['Module']	= Lib::config('module_list.module_menu');
		}
		
		$modules_perm		= Model_UserLevelPermission::instance()->get_module_function($user_level);		
		$modules_cols		= array_keys($modules_perm);
		
		$where_cond			= array();
		if(is_array($modules_cols)) {
			$buffers = array();
			foreach ($modules_cols as $cols) {
				$buffers[]	= strtolower($cols);
			}
			$where_cond		= array('module_name IN' => $buffers);
		}
		
		$where_cond	  = (is_array($where_cond)) ? array_merge($where_cond,array('parent_id' => 0)) : array('parent_id' => 0);
		
		$order_by	  = array('order' => 'ASC');
		
		$module_lists = $this->find($where_cond, $order_by);
		
		if(count($module_lists) != 0) {
			foreach($module_lists as $module) {
			
				$class_name	= $module->module_name;
				
				$where_cond		= array('parent_id'	=> $module->id);
				$order_by		= array('order'		=> 'ASC');
				
				$menu_modules	= $this->find($where_cond, $order_by);
				
				$buffers	= array();
				
				if(count($menu_modules) != 0) {
					foreach ($menu_modules as $menu) {
						$where_cond		= array('');
						$buffers[$menu->module_link]	= $menu->module_name;
					}
				}
				
				$modules[ucwords(str_replace('_', ' ', $class_name))]	= $buffers;
				unset($buffers);
			}
		}
	
		return $modules;
	}

}