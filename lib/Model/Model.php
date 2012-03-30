<?php
App::uses('ObjectRegistry', 'Utility');
App::uses('ConnectionManager', 'Model');

/**
 * Model class.
 */
class Model {

	/**
	 * belongsTo
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $belongsTo = array();
	
	/**
	 * hasMany
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $hasMany = array();
	
	/**
	 * hasOne
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $hasOne = array();
	
	/**
	 * hasAndBelongsToMany
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array();
	
	/**
	 * _table
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 */
	public $_table = null;
	
	/**
	 * _name
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 */
	public $_name = null;
	
	/**
	 * _dbConfig
	 * 
	 * (default value: 'default')
	 * 
	 * @var string
	 * @access public
	 */
	public $_dbConfig = 'default';
	
	/**
	 * _primaryKey
	 * 
	 * (default value: 'id')
	 * 
	 * @var string
	 * @access public
	 */
	public $_primaryKey = 'id';
	
	/**
	 * _associations
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access private
	 */
	private $_associations = null;
	
	/**
	 * recursive
	 * 
	 * (default value: -1)
	 * 
	 * @var float
	 * @access public
	 */
	public $recursive = -1;
	
	/**
	 * id
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 */
	public $id = null;
	
	/**
	 * data
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 */
	public $data = null;

	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
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
	
	/**
	 * __isset function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
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
	/**
	 * __get function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		
		throw new ModelNotFoundException('Trying to load a non-associated model!');
	}
	
	
	
	
	
	/**
	 * buildAssociationData function.
	 * 
	 * @access public
	 * @return void
	 */
	public function buildAssociationData() {
		$associations = array('belongsTo', 'hasMany', 'hasOne', 'hasAndBelongsToMany');
		$defaults = array();
		
		$this->_associations = array();
		foreach ($associations as $association) {
			if($association == 'hasMany' || $association == 'hasOne') {
				$defaults = array_merge($defaults, array('dependant' => false));
			}
			if (is_array($this->{$association})) {
				foreach ($this->{$association} as $key => $value) {
					if (!array_key_exists($association, $this->_associations)) {
						$this->_associations[$association] = array();
					}
					if (is_string($key) && is_array($value)) {
						$value = array_merge($defaults, $value);
						array_push($this->_associations[$association], array($key => $value));
					} elseif (is_numeric($key) && is_string($value)) {
						array_push($this->_associations[$association], array($value => $defaults));
					}
				}
			} else {
				array_push($this->_associations[$association], array($this->{$association} => $defaults));
			}
		}	
	}
	
	
	
	/**
	 * find function.
	 * 
	 * @access public
	 * @param string $type (default: 'all')
	 * @param array $query (default: array())
	 * @param bool $associated (default: false)
	 * @return void
	 */
	public function find($type = 'all', $query = array(), $associated = false) {
		if (!is_array($query)) {
			trigger_error('Query data must be an array…');
		}
		$db = $this->getDataSource();
		$results = array();
		switch($type) {
			case 'all':
				$results = $db->read($this, $query, false, $associated);
			break;
			case 'count':
				$results = $db->read($this, $query, true, $associated);
			break;
			case 'first':
				$query['limit'] = 1;
				$results = $db->read($this, $query, false, $associated);
				$results = $results[0];
			break;
			case 'last':
				$query['limit'] = 1;
				if (isset($query['order'])) {
					if (is_array($query['order'])) {
						foreach ($query['order'] as $key => $val) {
							if (is_numeric($key) && is_array($val)) {
								foreach ($val as $field => $order) {
									if ($order == 'ASC') {
										$query['order'][$key][$field] = 'DESC';
									} else {
										$query['order'][$key][$field] = 'ASC';
									}
								}
							} else {
								if ($val == 'ASC') {
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

				$results = $db->read($this, $query, false, $associated);
				$results = $results[0];
				break;
		}
		return $this->afterFind($results);
		
	}
	
	
	/**
	 * save function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function save($data, $whitelist = null, $saveAll = false) {
		$data = $this->beforeSave($data);
		$association_data = array();
		$out = array();
		if (is_array($data)) {
			$db = $this->getDataSource();
			
			foreach ($data as $key => $field) {
				if ($key === $this->_name || $saveAll) {
					foreach ($field as $fieldName => $val) {
						if (empty($val) || $val == '') {
							$val = null;
						}
						if (
							$whitelist !== null 
							&& is_array($whitelist) 
							&& !in_array($key . '.' . $fieldName, $whitelist)
						) {
							continue;
						}
						
						if (strpos($fieldName, '.') !== false) {
							list(,$fieldName) = explode('.', $fieldName);
						}
						if ($fieldName === $this->_primaryKey) {
							$this->id = $val;
						}
						if ($key === $this->_name)
							$out[$key . '.' . $fieldName] = $val;
						else
							if ($saveAll)
								$association_data[$key][$fieldName] = $val;
					}
				} else {
					continue;
				}
			}
			if (!empty($out)) {
				if ($this->id === null) {
					$fields = array();
					$values = array();
					foreach($out as $key => $val) {
						if (strpos($key, '.') !== false) {
							list(,$key) = explode('.', $key);
						}
						$fields[] = $key;
						$values[] = $val;
					}
					$db->create($this, $fields, $values);
					
				
				} else {
					if ($this->exists()) {
						$fields = array_keys($out);
						$values = array_values($out);
						
						$fieldKey = $this->_name . '.' . $this->_primaryKey;
						
						foreach($fields as $key => $value) {
							if ($value === $fieldKey) {
								if ($values[$key] === $this->id) {
									unset($fields[$key], $values[$key]);
								}
							}
						}
						
						$conditions = array(
							$fieldKey => $this->id
						);
						$db->update($this, $fields, $values, $conditions);
						
					} 
				
				}
				if ($this->id !== null) {
					if (count($association_data) > 0)
						$this->saveAssociatedData($association_data);
					return true;
				}
			}
			
			
		}
		return false;
	}
	
	public function saveAssociatedData($association_data) {
		foreach($association_data as $model => $associated_data) {
			$associationType = $this->getAssociationType($model);
			switch($associationType) {
				case 'hasOne':
					$foreignKey = strtolower($this->_name) . '_' . $this->_primaryKey;
					$associated_data[$foreignKey] = $this->id;
					
					$result = $this->{$model}->find('first', array(
						'recursive' => -1,
						'fields' => array(
							$this->{$model}->_name . '.' . $this->{$model}->_primaryKey
						),
						'conditions' => array(
							$this->{$model}->_name . '.' . $foreignKey => $this->id
						)
					));
					
					if ($result) {
						$associated_data[$this->{$model}->_primaryKey] = $result[$this->{$model}->_name][$this->{$model}->_primaryKey];
					}
					
					$this->{$model}->save(array($this->{$model}->_name => $associated_data), null, false);
				break;
				case 'belongsTo':

					$localKey = strtolower($this->{$model}->_name) . '_' . $this->{$model}->_primaryKey;
					
					$result = $this->read($localKey);
					if ($result) {
						$associated_data[$this->{$model}->_primaryKey] = $result[$this->_name][$localKey];
					}
					$this->{$model}->save(array($this->{$model}->_name => $associated_data), null, false);
				break;
				case 'hasMany':
					$foreignKey = strtolower($this->_name) . '_' . $this->_primaryKey;
					$associated_data[$foreignKey] = $this->id;
					
					$results = $this->{$model}->find('all', array(
						'recursive' => -1,
						'fields' => array(
							$this->{$model}->_name . '.' . $this->{$model}->_primaryKey
						),
						'conditions' => array(
							$this->{$model}->_name . '.' . $foreignKey => $this->id
						)
					));
					if(is_array($results)) {
						foreach ($results as $result) {
							$associated_data[$this->{$model}->_primaryKey] = $result[$this->{$model}->_name][$this->{$model}->_primaryKey];
							$this->{$model}->save(array($this->{$model}->_name => $associated_data), null, false);
						}
					} elseif ($results === false) {
						$this->{$model}->save(array($this->{$model}->_name => $associated_data), null, false);
					}
					
				break;
			}
			
			if (
				$associationType == 'belongsTo'
				|| $associationType == 'hasAndBelongsToMany'
			) {
				$foreignKey = strtolower($this->{$model}->_name) . '_' . $this->{$model}->_primaryKey;
				$data[$foreignKey] = $this->{$model}->id;
				$data[$this->_primaryKey] = $this->id;
				$this->save(array($this->_name => $data), null, false);
			}
			
			
		}
	}
	
	public function getAssociationType($model) {
		foreach($this->_associations as $type => $models) {
	
			foreach($models as $key => $val) {
				$theModel = array_keys($val);
				$theModel = array_shift($theModel);
				if ($model === $theModel) {
					return $type;
				}
			}
		}
		return false;
	}
	
	public function delete($id = null, $cascade = false) {
		if ($id !== null) {
			$this->id = $id;
			if ($this->exists()) {
				$conditions = array(
					$this->_primaryKey => $this->id
				);
				$this->getDataSource()->delete($this, $conditions);
			}
		}
		return false;
	}
	
	
	
	/**
	 * create function.
	 * 
	 * @access public
	 * @return void
	 */
	public function create() {
		$this->id = null;
		$this->data = null;
	}
	
	/**
	 * exists function.
	 * 
	 * @access public
	 * @param mixed $id (default: null)
	 * @return void
	 */
	public function exists($id = null) {
		if ($id === null) {
			$id = $this->id;
		}
		$count = $this->find('count', array(
			'conditions' => array(
				$this->_name . '.' . $this->_primaryKey => $id
			)
		));
		if ($count) {
			return true;
		}
		return false;
	}
	
	public function read($fields = null) {
		$this->data = null;
		if ($this->id !== null) {
			if ($fields === null) {
				$fields = array();
			}
			$this->data = $this->find('first', array(
				'fields' => $fields,
				'conditions' => array(
					$this->_name . '.' . $this->_primaryKey => $this->id
				),
				'recursive' => $this->recursive
			));
			if ($this->data) {
				return $this->data;
			}
		}
		
		return false;
	}
	
	/**
	 * afterFind function.
	 * 
	 * @access public
	 * @param mixed $results
	 * @return void
	 */
	public function afterFind($results) {
		return $results;
	}
	
	/**
	 * beforeSave function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function beforeSave($data) {
		return $data;
	}
	
	
	/**
	 * getLastStatement function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getLastStatement() {
		$db = $this->getDataSource();
		return $db->_lastStatement;
	}
	
	/**
	 * getDataSource function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getDataSource() {
		return ConnectionManager::getDataSource($this->_dbConfig);
	}
	
	/**
	 * _loadModel function.
	 * 
	 * @access private
	 * @param mixed $class
	 * @return void
	 */
	private function _loadModel($class) {

		
		$this->{$class} = ObjectRegistry::init($class);
		
		if ($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}

}