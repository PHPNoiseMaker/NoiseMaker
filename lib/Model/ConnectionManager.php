<?php

class ConnectionManager {
	
	private static $_instance = null;
	
	private static $_connections = array();
	
	private static $_datasource = null;
	
	public function __construct() {
			
	}
	
	public function __clone() {
	}
	
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		if(self::$_datasource === null) {
			App::import('database', 'Config');
			self::$_datasource = new DATABASE();
		}
		return self::$_instance;
	}
	
	private function add($key, $object) {
		if(!array_key_exists($key, self::$_connections)) {
			self::$_connections[$key] = $object;
		}
		return self::$_connections[$key];
	}
	
	private function get($key) {
		if(array_key_exists($key, self::$_connections)) {
			return self::$_connections[$key];
		}
		return null;
	}
	
	private function getDatasrc($key) {
		if(isset(self::$_datasource->{$key})) {
			return self::$_datasource->{$key};
		} else {
			trigger_error('Datasource not found!');
		}
	}
	
	public static function getDatasource($key) {
		return self::getInstance()->getDatasrc($key);
	}
	

	

}