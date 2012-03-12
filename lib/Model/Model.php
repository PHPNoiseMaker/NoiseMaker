<?php
App::uses('ObjectRegistry', 'Utility');
class Model {

	public $belongsTo = array();
	
	public $hasMany = array();
	
	public $hasOne = array();
	
	public $hasAndBelongsToMany = array();
	
	private $_associations = array(
		'belongsTo' => array(),
		'hasMany' => array(),
		'hasOne' => array(),
		'hasAndBelongsToMany' => array()
	);
	public function __construct() {
		foreach($this->_associations as $key => $value) {
			
			$this->_associations[$key] = array_values($this->{$key});
			
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

	
	private function _constructAssociations() {
		
	}
	
	private function _loadModel($class) {

		
		$this->{$class} = ObjectRegistry::init($class);
		
		if($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}

}