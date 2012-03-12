<?php

/**
 * Config class.
 */
class Config {

	
	/**
	 * _configs
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_configs = array();
	
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
		if(self::$instance === null) {
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
		if(array_key_exists($key, self::$_configs)) {
			return self::$_configs[$key];
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
		self::$_configs[$key] = $value;
		return true;
	}
	
	/**
	 * getObject function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $key
	 * @return void
	 */
	public static function getConfig($key) {
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
	public static function storeConfig($key, $value) {
		return self::getInstance()->set($key, $value);
	}
	
	/**
	 * getConfigs function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getConfigs() {
		return array_keys(self::$_configs);
	}

}