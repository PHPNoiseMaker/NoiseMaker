<?php

class ConnectionManager {
	
	private static $_instance = null;
	
	private static $_dataSources = array();
	
	private static $_config = null;
	
	private static $_statementHistory = array();
	
	public function __construct() {
			
	}
	
	public function __clone() {
	}
	
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		if (self::$_config === null) {
			App::import('database', 'Config');
			self::$_config = new DATABASE();
		}
		return self::$_instance;
	}
	
	private static function add($key, $object) {
		if (!array_key_exists($key, self::$_dataSources)) {
			self::$_dataSources[$key] = $object;
		}
		return self::$_dataSources[$key];
	}
	
	private function get($key) {
		if (array_key_exists($key, self::$_dataSources)) {
			return self::$_dataSources[$key];
		}
		return null;
	}
	
	private function getDatasrc($key) {
		return self::loadDatasource($key);
	
	}
	private static function getConnectionInfo($name) {
		if (isset(self::$_config->{$name}['datasource'])) {
			$classname = self::$_config->{$name}['datasource'];
			if (strpos($classname, '/') !== false) {
				$package = dirname($classname);
				$classname = basename($classname);
				return compact('package', 'classname');
			}
			return $classname;
		}
		return false;
	}
	private static function loadDatasource($name) {
		if (!array_key_exists($name, self::$_dataSources)) {
			$conInfo = self::getConnectionInfo($name);
			App::uses($conInfo['classname'], 'Model' . DS . 'Datasource' . DS . $conInfo['package']);
			if (class_exists($conInfo['classname'])) {
				return self::add($name, new $conInfo['classname'](self::$_config->{$name}));
			}
		}
		return self::$_dataSources[$name];
	}
	
	public static function getDataSource($key) {
		return self::getInstance()->getDatasrc($key);
	}
	
	private static function addHistory($sql) {
		self::$_statementHistory[] = $sql;
	}
	
	private static function getHistory($id = null) {
		if($id === null) {
			return self::$_statementHistory;
		}
		if(array_key_exists($id, self::$_statementHistory)) {
			return self::$_statementHistory[$id];
		}
		return null;
	}
	
	public static function record($sql, $params) {
		self::getInstance()->addHistory(array($sql, $params));
		return true;
	}
	
	public static function history($id = null) {
		return self::getInstance()->getHistory($id);
	}
	

	

}