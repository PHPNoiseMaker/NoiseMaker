<?php

/**
 * App class.
 *	Responsible for loading most files.
 *
 */
class App {
	
	/**
	 * _openFiles
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_openFiles = array();
	
	/**
	 * _map
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_map = array();
	
	
	/**
	 * __clone function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __clone() {
	
	}
	
	
	/**
	 * autoLoad function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $class
	 * @return void
	 */
	public static function autoLoad($class) {
		if(!isset(self::$_map[$class])) {
			return false;
		}
		if(array_key_exists(self::$_map[$class], self::$_openFiles)) {
			throw new Exception('File already open!');
		}
		return self::import($class, self::$_map[$class]);
	}
	
	/**
	 * uses function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $class
	 * @param mixed $path
	 * @return void
	 */
	public static function uses($class, $path) {
		self::$_map[$class] = $path;
	}

	/**
	 * import function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $class
	 * @param mixed $path (default: null)
	 * @param bool $check_only (default: false)
	 * @return void
	 */
	public static function import($class, $path = null, $check_only = false) {
		if(strpos('/', $path) !== false) {
			$path = implode(DS, explode('/', $path));
		}
		if(file_exists($file = ROOT . DS . APP_DIR . DS . $path . DS . $class . '.php')) {
			if(!$check_only) {
				include_once APP_DIR . DS . $path . DS . $class . '.php';
				self::$_openFiles[] = $file;
			}

		} elseif(file_exists($file = ROOT . DS . 'lib' . DS . $path . DS . $class . '.php')) {
			if(!$check_only) {
				include_once 'lib' . DS . $path . DS . $class . '.php';
				self::$_openFiles[] = $file;
			}
			
		} else {
			if(!$check_only) {
				switch($path) {
					case 'Controller':
						throw new ControllerNotFoundException();
						break;
					case 'Model':
						throw new ModelNotFoundException();
						break;
					default:
						throw new ClassNotFoundException();
						break;
				}
				
			}
			else
				return false;
		}
		
		return true;
	
	}
}