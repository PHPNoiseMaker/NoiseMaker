<?php
App::uses('ObjectRegistry', 'Utility');
App::uses('ConnectionManager', 'Model');

class Model {

	public $belongsTo = array();
	
	public $hasMany = array();
	
	public $hasOne = array();
	
	public $hasAndBelongsToMany = array();
	
	public $_table = null;
	
	public $_name = null;
	
	public $_dbConfig = 'default';
	
	private $_associations = array(
		'belongsTo' => array(),
		'hasMany' => array(),
		'hasOne' => array(),
		'hasAndBelongsToMany' => array()
	);
	public function __construct() {
		if($this->_name === null || !isset($this->_name)) {
			$this->_name = get_class($this);
		}
		if($this->_table === null || !isset($this->_table)) {
			$this->_table = Inflect::pluralize(strtolower($this->_name));
		}
	}
	public function __isset($name) {
		
		foreach($this->_associations as $key => $relationship) {
			if(array_search($name, $this->{$key}) !== false) {
				$this->{$name} = ObjectRegistry::init($name, $relationship);
				break;
			}
		}
		if(isset($this->{$name}) && $this->{$name} instanceOf Model) {
			return true;
		}
		return false;
	}
	public function __get($name) {
		if(isset($this->{$name})) {
			return $this->{$name};
		}
		
		throw new ModelNotFoundException('Trying to load a non-associated model!');
	}

	
	
	private function _loadModel($class) {

		
		$this->{$class} = ObjectRegistry::init($class);
		
		if($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}

}