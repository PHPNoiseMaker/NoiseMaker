<?php
/**
 * ObjectRegistry class.
 */
class ObjectRegistry {

	/**
	 * _objects
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_objects = array();
	
	/**
	 * instance
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access private
	 * @static
	 */
	private static $instance = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
	 
	}
	
	/**
	 * __clone function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __clone() {

	}
	
	/**
	 * getInstance function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * get function.
	 * 
	 * @access protected
	 * @param mixed $key
	 * @return void
	 */
	protected function get($key) {
		if (array_key_exists($key, self::$_objects)) {
			return self::$_objects[$key];
		}
		return null;
	}
	
	/**
	 * set function.
	 * 
	 * @access protected
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	protected function set($key, $value) {
		if (!array_key_exists($key, self::$_objects)) {
			self::$_objects[$key] = $value;
		}
		return self::$_objects[$key];
	}
	
	/**
	 * getObject function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $key
	 * @return void
	 */
	public static function getObject($key) {
		return self::getInstance()->get($key);
	}
	
	/**
	 * storeObject function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public static function storeObject($key, $value) {
		return self::getInstance()->set($key, $value);
	}
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $class
	 * @return void
	 */
	public static function init($class) {
		if (!array_key_exists($class, self::$_objects)) {
			App::uses($class, 'Model');
			if (class_exists($class)) {
				$modelRef = new ReflectionClass($class);
				
				if ($modelRef->isAbstract() || $modelRef->isInterface()) {
					return false;
				}
				$model = self::storeObject($class, $modelRef->newInstance());
				if ($model instanceOf Model) {
					return $model;
				}
			}
			return false;
		}
		return self::$_objects[$class];
	}
	
	/**
	 * getObjects function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getObjects() {
		return array_keys(self::$_objects);
	}

}