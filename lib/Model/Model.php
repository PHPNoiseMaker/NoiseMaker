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
			break;
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

				$results = $db->read($this, $query, false, $associated);
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
	public function save($data) {
		if (is_array($data)) {
			$db = $this->getDataSource();
			foreach($data as $key => $val) {
				if($db->fieldBelongsToModel($key, $this)) {
					if(strpos($key, '.') !== false) {
						list(,$key) = explode('.', $key);
					}
					if($key === $this->_primaryKey) {
						$this->id = $val;
					}
					
				} else {
					unset($data[$key]);
				}
				
			}
			
			if ($this->id === null) {
				$fields = array();
				$values = array();
				foreach($data as $key => $val) {
					if(strpos($key, '.') !== false) {
						list(,$key) = explode('.', $key);
					}
					$fields[] = $key;
					$values[] = $val;
				}
				return $db->create($this, $fields, $values);
			
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
	}
	
	/**
	 * exists function.
	 * 
	 * @access public
	 * @param mixed $id (default: null)
	 * @return void
	 */
	public function exists($id = null) {
		if($id === null) {
			$id = $this->id;
		}
		$count = $this->find('count', array(
			'conditions' => array(
				$this->_name . '.' . $this->_primaryKey => $id
			)
		));
		if($count) {
			return true;
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
	 * afterSave function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function afterSave($data) {
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