<?php
App::uses('ObjectRegistry', 'Utility');
class Model {

	public $belongsTo = array();
	
	public $hasMany = array();
	
	public $hasOne = array();
	
	public $hasAndBelongsToMany = array();
	
	public function __isset($name) {
		if(array_search($name, $this->belongsTo) !== false) {
			$this->{$name} = ObjectRegistry::init($name);
			return true;
		}
		if(array_search($name, $this->hasMany) !== false) {
			$this->{$name} = ObjectRegistry::init($name);
			return true;
		}
		if(array_search($name, $this->hasOne) !== false) {
			$this->{$name} = ObjectRegistry::init($name);
			return true;
		}
		if(array_search($name, $this->hasAndBelongsToMany) !== false) {
			$this->{$name} = ObjectRegistry::init($name);
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
	
	public function __construct() {
		
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