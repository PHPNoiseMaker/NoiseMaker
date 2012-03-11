<?php
class ObjectRegistry {

	private static $_objects = array();
	
	private static $instance = null;
	
	public function __construct() {
	 
	}
	
	public function __clone() {
	
	}
	
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	protected function get($key) {
		$_this = self::getInstance();
		if(array_key_exists($key, $_this->_objects)) {
			return $_this->_objects[$key];
		}
		return null;
	}
	
	protected function set($key, $value) {
		if(!array_key_exists($key, self::$_objects)) {
			self::$_objects[$key] = $value;
		}
		return $_this->_objects[$key];
	}
	
	public static function getObject($key) {
		return self::getInstance()->get($key);
	}
	
	public static function storeObject($key, $value) {
		return self::getInstance()->set($key, $value);
	}

}