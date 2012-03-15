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
	
	private $_associations = null;
	public function __construct() {
		if ($this->_name === null || !isset($this->_name)) {
			$this->_name = get_class($this);
		}
		if ($this->_table === null || !isset($this->_table)) {
			$this->_table = Inflect::pluralize(strtolower($this->_name));
		}
		if ($this->_associations === null) {
			$this->buildAssociationData();
		}
	}
	
	public function buildAssociationData() {
		$associations = array('belongsTo', 'hasMany', 'hasOne', 'hasAndBelongsToMany');
		foreach ($associations as $association) {
			if (is_array($this->{$association})) {
				foreach ($this->{$association} as $key => $value) {
					if (is_string($key) && is_array($value)) {
						$this->_associations[$association] = array($key => $value);
					} elseif(is_numeric($key) && is_string($value)) {
						$this->_associations[$association] = array($value => array());
					}
				}
			} else {
				$this->_associations[$association] = array($this->{$association} => array());
			}
		}		
	}
	
	public function __isset($name) {
		foreach($this->_associations as $key => $association) {
			foreach ($association as $key => $relationship) {
				if ($name === $key) {
					$this->{$name} = ObjectRegistry::init($key);
					break;
				}
			}
		}
		if (isset($this->{$name}) && $this->{$name} instanceOf Model) {
			return true;
		}
		return false;
	}
	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		
		throw new ModelNotFoundException('Trying to load a non-associated model!');
	}

	
	
	private function _loadModel($class) {

		
		$this->{$class} = ObjectRegistry::init($class);
		
		if ($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}

}