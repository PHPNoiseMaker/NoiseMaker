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
	
	public $_primaryKey = 'id';
	
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
		$this->_associations = array();
		foreach ($associations as $association) {
			if (is_array($this->{$association})) {
				foreach ($this->{$association} as $key => $value) {
					if(!array_key_exists($association, $this->_associations)) {
						$this->_associations[$association] = array();
					}
					if (is_string($key) && is_array($value)) {
						array_push($this->_associations[$association], array($key => $value));
					} elseif(is_numeric($key) && is_string($value)) {
						array_push($this->_associations[$association], array($value => array()));
					}
				}
			} else {
				array_push($this->_associations[$association], array($this->{$association} => array()));
			}
		}		
	}
	
	public function __isset($name) {
		
		foreach($this->_associations as $key => $association) {
			foreach ($association as $model) {
				foreach ($model as $key => $relationship) {
					if ($name === $key) {
						$this->{$name} = ObjectRegistry::init($key);
						break;
					}
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
	
	public function find($type = 'all', $query) {
		if (!is_array($query)) {
			trigger_error('Query data must be an array…');
		}
		$db = ConnectionManager::getDataSource('default');
		switch($type) {
			case 'all':
				return $db->read($this, $query);
			case 'count':
				return $db->read($this, $query, true);
			case 'first':
				$query['limit'] = 1;
				$result = $db->read($this, $query);
				return $result[0];
			case 'last':
				$query['limit'] = 1;
				if (isset($query['order'])) {
					if (is_array($query['order'])) {
						foreach ($query['order'] as $key => $val) {
							if(is_numeric($key) && is_array($val)) {
								foreach ($val as $field => $order) {
									if($order == 'ASC') {
										$query['order'][$key][$field] = 'DESC';
									} else {
										$query['order'][$key][$field] = 'ASC';
									}
								}
							} else {
								if($val == 'ASC') {
									$query['order'][$field] = 'DESC';
								} else {
									$query['order'][$field] = 'ASC';
								}
							}
						}
					}
				} else {
					$query['order'] = array(array($this->_name . '.' . $this->_primaryKey => 'DESC'));
				}
				var_dump($query['order']);
				$result = $db->read($this, $query);
				return $result[0];
		}
		
	}
	
	public function getLastStatement() {
		$db = ConnectionManager::getDataSource('default');
		return $db->_lastStatement;
	}
	
	
	private function _loadModel($class) {

		
		$this->{$class} = ObjectRegistry::init($class);
		
		if ($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}

}