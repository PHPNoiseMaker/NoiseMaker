<?php
App::uses('DataSource', 'Model/Datasource');
class PdoSource extends DataSource{ 
	protected $_handle = null;
	
	protected $_params = array();
	
	protected $_order = null;
	
	protected $_limit = null;
	
	protected $_joins = null;
	
	protected $_fields = null;
	
	protected $_values = null;
	
	protected $_table = null;
	
	protected $_alias = null;
	
	protected $_conditions = null;
	
	protected $_groupBy = null;
	
	protected $quote = '`';
	
	protected $_associationFields = array();
	
	public $_lastStatement = null;
	
	
	public function buildJoinStatement($data) {
		return trim("{$data['type']} JOIN {$data['table']} AS {$data['alias']} ON ({$data['conditions']})");
	}
	
	
	public function buildStatement($type) {
		switch($type) {
			case 'select':
				$query = "SELECT {$this->_fields} "
					   . "FROM `{$this->_table}` AS {$this->_alias}"
					   . " {$this->_joins}"
					   . " {$this->_conditions}"
					   . " {$this->_groupBy}"
					   . " {$this->_order}"
					   . " {$this->_limit}";
				return $query;
			
			case 'update':
				$query = "UPDATE {$this->_table} AS {$this->_alias}"
					   . " {$this->_joins}"
					   . " SET {$this->_fields}"
					   . " {$this->_conditions}";
				return $query;
			
			case 'insert':
				return "INSERT INTO {$this->_table} ({$this->_fields}) VALUES ({$this->_values})";
			
			case 'delete':
				return "DELETE FROM {$this->_table} {$this->_joins} {$this->_conditions}";

			
		}
	}
	
	private function createPlaceHolders($type = '_fields') {
		switch($type) {
			default:
				$type = '_fields';
				break;
			case 'params':
				$type = '_params';
				break;
		}
		if ($this->{$type} !== null) {
			$count = count(explode(',', $this->{$type}));
			$return = '?';
			for($i = 1; $i < $count; $i++) {
				$return .= ',?';
			}
			return $return;
		}
		return null;
	}
	
	public function __construct($config, $connect = true) {
		parent::__construct($config);
		if ($connect) {
			$this->connect();
		}
	}
	
	public function prepare($sql, $params = null) {
		if ($this->_connection === null) {
			$this->connect();
		}
		if ($params !== null) {
			$this->_params = $params;
		} else {
			$params = $this->_params;
		}
		
		$this->_handle = $this->_connection->prepare($sql);
		$this->_lastStatement = $sql;
		if(Config::getConfig('debug') > 1)
			ConnectionManager::startRecord($sql, $params);
	}
	
	public function execute() {
		$this->_handle->execute($this->_params);
		if(Config::getConfig('debug') > 1)
			ConnectionManager::endRecord($this->getAffected());
	}
	
	public function setFetch($type = 'assoc') {
		switch($type) {
			case 'assoc':
			default:
				$fetch = PDO::FETCH_ASSOC;
				break;
			case 'both':
				$fetch = PDO::FETCH_BOTH;
				break;
			case 'object':
				$fetch = PDO::FETCH_OBJ;
				break;
			case 'num':
				$fetch = PDO::FETCH_NUM;
				break;
			case 'bound':
				$fetch = PDO::FETCH_BOUND;
				break;
		}
		$this->_handle->setFetchMode($fetch);
	}

	public function fetch() {
		return $this->_handle->fetch();
	}
	
	public function releaseResources() {
		$this->_params = array();
		$this->_order = null;
		$this->_limit = null;
		$this->_joins = null;
		$this->_fields = null;
		$this->_values = null;
		$this->_table = null;
		$this->_alias = null;
		$this->_conditions = null;
		$this->_handle = null;
		//$this->_connection = null;
	}
	
	/**
	 * create function.
	 * 
	 * @access public
	 * @param Model &$model
	 * @param mixed $fields
	 * @param mixed $values
	 * @return void
	 */
	public function create(Model &$model, $fields, $values) {
		$this->releaseResources();
		if (is_array($fields) && is_array($values)) {
			$fieldCount = count($fields);
			$valueCount = count($values);
			if ($fieldCount === $valueCount) {
				foreach ($values as $key => $value) {
					$values[$key] = $this->placeHold($value);
				}
				foreach ($fields as $key => $field) {
					$fields[$key] = $this->fieldQuote($field);
				}
				$this->_fields = implode(',', $fields);
				$this->_values = implode(',', $values);
			} else {
				return false;
			}
			$this->_table = $this->fieldQuote($model->_table);
		} else {
			return false;
		}
		$query = $this->buildStatement('insert');
		
		$this->prepare($query);
		$this->execute();
		$id = $this->getLastInsertId();
		$model->id = $id;
	}
	
	/**
	 * read function.
	 * 
	 * @access public
	 * @param Model &$model
	 * @param array $queryData (default: array())
	 * @param bool $count (default: false)
	 * @param bool $associated (default: false)
	 * @return void
	 */
	public function read(Model &$model, $queryData = array(), $count = false, $associated = false) {
		$this->releaseResources();
		if (is_array($queryData)) {
			$this->_table = $model->_table;
			$this->_alias = $this->quote . $model->_name . $this->quote;
			
			if (isset($queryData['limit'])) {
				$this->_limit = $this->limit($queryData['limit']);
			}
			
			if (isset($queryData['order'])) {
				$this->order($queryData['order']);
			}
			
			if (isset($queryData['fields'])) {
				$this->fields($queryData['fields'], $model);
			}  else {
				$this->_fields = '*';
			}
			if ($count) {
				$this->_fields = 'COUNT(' . $this->fieldQuote($model->_name . '.' . $model->_primaryKey) . ')';
			}
			
			if (isset($queryData['recursive']) && $queryData['recursive'] > -1) {
				$model->recursive = $queryData['recursive'];
			}
			
			//Joins MUST be done before conditions. Order matters.
			
			// Joins
			
			if ($model->recursive > -1 && !$count) {
				$this->fetchJoins($model, $model->recursive);
			}
			
			
			
			// Conditions
			
			if (isset($queryData['conditions'])) {
				$this->_conditions = $this->parseConditions($queryData['conditions']);
			}
			
			if (isset($queryData['group'])) {
				$this->buildGroupBy($queryData['group']);
			}
			
			
			
			$sql = $this->buildStatement('select');
			
			
			$this->prepare($sql);
			
			$this->execute();
			$results = $this->fetchResults($count, $associated);
			
			if ($model->recursive > -1 && !$count) {
				
				$results = $this->fetchAssociations($model, $results, $model->recursive);
			}
			if (!empty($results))
				return $results;
			return false;
			
		}
		trigger_error('Query data must be an array...');
	}
	
	/**
	 * update function.
	 * 
	 * @access public
	 * @param Model &$model
	 * @param mixed $fields
	 * @param mixed $values
	 * @param mixed $conditions
	 * @return void
	 */
	public function update(Model &$model, $fields, $values, $conditions) {
		$this->releaseResources();
		if (is_array($fields) && is_array($values)) {
			$fieldCount = count($fields);
			$valueCount = count($values);
			if ($fieldCount === $valueCount) {
				$this->_fields = implode(',', $this->updateFields($fields, $values));
			} else {
				return false;
			}
			$this->_table = $this->fieldQuote($model->_table);
			$this->_alias = $this->fieldQuote($model->_name);
			$this->_conditions = $this->parseConditions($conditions);
		} else {
			return false;
		}
		$query = $this->buildStatement('update');
		//var_dump($query, $this->_params);
		$this->prepare($query);
		$this->execute();
		return $this->getAffected();
	}
	
	/**
	 * delete function.
	 * 
	 * @access public
	 * @param Model &$model
	 * @param mixed $conditions
	 * @return void
	 */
	public function delete(Model &$model, $conditions) {
		$this->releaseResources();
		if (is_array($conditions)) {
			$this->_conditions = $this->parseConditions($conditions);
			$this->_table = $model->_table;
			
			$query = $this->buildStatement('delete');
			$this->prepare($query);
			$this->execute();
			return $this->getAffected();
		}
		return false;
	}
	
	/**
	 * order function.
	 * 
	 * @access public
	 * @param mixed $order
	 * @return void
	 */
	public function order($order) {
		if (is_array($order)) {
			if ($this->_order === null) {
				$this->_order = 'ORDER BY ' . $this->setOrder($order[0]);
			}
			for($i = 1; $i < count($order); $i++) {
				$this->_order .= ', ' . $this->setOrder($order[$i]);
			}
		} else {
			$this->_order = $order;
		}
	}
	
	/**
	 * updateFields function.
	 * 
	 * @access public
	 * @param mixed $fields
	 * @param mixed $values
	 * @return void
	 */
	public function updateFields($fields, $values) {
		if (is_array($fields) && is_array($values)) {
			$fieldCount = count($fields);
			if ($fieldCount === count($values)) {
				$out = array();
				for($i = 0; $i < $fieldCount; $i++) {
					$out[] = $this->fieldQuote($fields[$i]) . ' = ' . $this->placeHold($values[$i]);
				}
				return $out;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * fields function.
	 * 
	 * @access public
	 * @param mixed $fields
	 * @param Model $model
	 * @return void
	 */
	public function fields($fields, Model $model) {
		if (is_array($fields)) {
			$this->_fields = '';
			foreach($fields as $field) {
				if (!empty($field)) {
					if ($this->fieldBelongsToModel($field, $model)) {
						$this->_fields .= $this->fieldQuote($field) . ',';
						
					} else {
						$this->_associationFields[] = $field;
					}
					
				}
			}
			if (empty($this->_fields)) {
				$this->_fields = '*';
			}

		} elseif (!empty($fields)) {
			$this->_fields = $this->fieldQuote($fields);
		} else {
			$this->_fields = '*';
		}
		if (substr($this->_fields, strlen($this->_fields) - 1) == ',') {
			$this->_fields = substr($this->_fields, 0, strlen($this->_fields) -1);
		}
		
	}
	/**
	 * limit function.
	 * 
	 * @access public
	 * @param mixed $limit
	 * @return void
	 */
	public function limit($limit) {
		if (is_int($limit))
			return 'LIMIT 0,' . $limit;
		elseif (is_string($limit)) {
			if (strpos($limit, ',') !== false) {
				list($start, $end) = explode(',', $limit);
				return  "LIMIT {$start}, {$end}";
			} else {
				return  'LIMIT 0, ' . (int) $limit;
			}
		}
	}
	
	
	/**
	 * setOrder function.
	 * 
	 * @access public
	 * @param mixed $order
	 * @return void
	 */
	public function setOrder($order) {
		if (is_array($order)) {
			foreach ($order as $key => $val) {
				return $this->fieldQuote($key) . ' ' . strtoupper($val);
			}
		} else {
			return $order;
		}
	}
	
	/**
	 * parseConditions function.
	 * 
	 * @access public
	 * @param mixed $conditions
	 * @param bool $where (default: true)
	 * @param bool $params (default: true)
	 * @return void
	 */
	public function parseConditions($conditions, $where = true, $params = true) {		
		if ($where) {
			$where = ' WHERE ';
		}
		
		if (is_array($conditions) && !empty($conditions)) {
			$out = $this->parseConditionArray($conditions, $params);
			return $where . implode(' AND ', $out);
			
		} elseif (empty($where)) {
			return $where . '1 = 1';
		}
		return  $where . $this->fieldQuote($conditions, $params);
	}
	
	/**
	 * parseConditionArray function.
	 * 
	 * @access public
	 * @param mixed $conditions
	 * @param bool $params (default: false)
	 * @return void
	 */
	public function parseConditionArray($conditions, $params = false) {
		$out = array();
		$commands = array('AND', 'XOR', 'NOT', 'OR', '||', '&&');
	
		foreach ($conditions as $key => $value) {
			$join = ' ' . $commands[0] . ' ';
			$not = null;
	
			if (is_numeric($key) && empty($value)) {
				continue;
			} elseif (is_numeric($key) && is_string($value)) {
				if (!$join)
					$out[] = $not . $this->fieldQuote($value);
				else
					$out[] = $not . $value;
			} elseif (
				(is_numeric($key) && is_array($value)) 
				|| in_array(strtoupper(trim($key)), $commands)
			) {
				if (in_array(strtoupper(trim($key)), $commands)) {					
					$join = ' ' . strtoupper($key) . ' ';
				} else {
					$key = $join;
				}
				$value = $this->parseConditionArray($value, $params);

				if (strpos($join, $commands[2]) !== false) {
					if (strtoupper(trim($key)) === $commands[2]) {
						$key = ' ' . $commands[0] . ' ' . strtoupper(trim($key));
					}
					$not = ' ' . $commands[2] . ' ';
				}
			
				if (count($value) <= 1) {
					if ($not) {
						$out[] = $not . '( ' . $value[0] . ' )';
					} else {
						$out[] = $value[0] ;
					}
				} else {
					$implode = ') ' . strtoupper($key) . ' (';
					$out[] = '(' . $not . '(' . implode($implode, $value) . ' ))';
				}
			} else {
				$out[] = $this->_parseKey(trim($key), $value, $params);	
			}
			 
		}
		return $out;
	}
	
	/**
	 * _parseKey function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $params (default: false)
	 * @return void
	 */
	public function _parseKey($key, $value, $params = false) {
		$operators = array('!=', '>=', '<=', '<', '>', '=', 'LIKE');
		foreach ($operators as $operator) {
			if (strpos(strtoupper($key), $operator) !== false) {
				$key = trim(substr($key, 0, strlen($key) - strlen($operator)));
				$key = $this->fieldQuote($key);
				
				if ($operator == '!=') {
					if (empty($value) || $value === null || $value == 'null') {
						return $key . ' IS NOT NULL';
					}
				}
				if ($operator == '=') {
					if (empty($value) || $value === null || $value == 'null') {
						return $key . ' IS NULL';
					}
				}
				if ($params) {
					$this->_params[] = $value;
					return $key . " {$operator} ?";
				} else {
					return $key . " {$operator} " . $value;
				}
			}
		}
		if ($params) {
			$this->_params[] = $value;
			return $this->fieldQuote($key) . " = ?";
		} else{
			return $this->fieldQuote($key) . " = " . $value;
		} 
		
	}
	

	/**
	 * Fetches hasOne and belongsTo Associations .
	 * 
	 * @access public
	 * @param Model $model
	 * @param mixed $recursive
	 * @return void
	 */
	public function fetchJoins(Model $model, $recursive) {
		foreach ($model->_associations as $association => $value) {
			foreach ($value as $key => $val) {
				foreach ($val as $associatedModel => $relationship) {
					if ($association === 'hasOne' || $association === 'belongsTo') {
						$targetAlias = $model->{$associatedModel}->_name;
						$defaults = array(
							'type' => 'LEFT',
							'foreign_key' => strtolower($targetAlias) . '_id',
						);
						$settings = array_merge($defaults, $relationship);
						
						foreach ($this->_associationFields as $association_field) {
							if (strpos($association_field, '.') !== false) {
								list($aModel, $aFieldKey) = explode('.', $association_field);
								if($aModel === $associatedModel) {
									$this->_fields .= ', ' . $this->fieldQuote($association_field);
								}
							}
						}
						
						if ($association === 'belongsTo') {
							$joinKey = $model->_name . '.' . $settings['foreign_key'];
							$joinValue = $this->fieldQuote(
								$targetAlias 
								. '.' 
								. $model->{$associatedModel}->_primaryKey
							);
						} else {
							$joinKey = $model->_name . '.' . $model->_primaryKey;
							$joinValue = $this->fieldQuote(
								$targetAlias 
								. '.' 
								. strtolower($model->_name) . '_' . $model->_primaryKey
							);

						}
						$conditions = array(array($joinKey => $joinValue));
						if (array_key_exists('conditions', $settings)) {
							if (is_array($settings['conditions'])) {
								
								$conditions[] = $this->parseConditions($settings['conditions'], false);

							}
						}
						
						$data = array(
							'type' => strtoupper($settings['type']),
							'table' => $this->fieldQuote($model->{$associatedModel}->_table),
							'alias' => $this->fieldQuote($targetAlias),
							'conditions' => $this->parseConditions($conditions, false, false)
						);
						$this->_joins .= ' ' . $this->buildJoinStatement($data);
						
					}
				}	
			}	
		}
	}
	

	/**
	 * Fetches hasMany and hasAndBelongsToMany associations
	 * 
	 * @access public
	 * @param Model $model
	 * @param mixed $results
	 * @param mixed $recursive
	 * @return void
	 */
	public function fetchAssociations(Model $model, $results, $recursive) {
		foreach ($model->_associations as $association => $value) {
			foreach ($value as $key => $val) {
				foreach ($val as $associatedModel => $relationship) {
					if ($association === 'hasMany' || $association === 'hasAndBelongsToMany') {
						foreach ($results as $key => $result) {
							$targetAlias = $model->{$associatedModel}->_name;
							$defaults = array(
								'foreign_key' => 'id',
								'limit' => 100
							);
							$settings = array_merge($defaults, $relationship);
							
							$joinKey = $model->{$associatedModel}->_name . '.' . strtolower($model->_name) . '_' . $model->_primaryKey;
							$joinValue = $result[$model->_name][$model->_primaryKey];
							$conditions = array(array($joinKey => $joinValue));
							
							
							$data = array(
								'conditions' => $conditions,
								'recursive' => -1,
								'fields' => $this->_associationFields,
								'limit' => $settings['limit']
							);
							$results[$key][$model->{$associatedModel}->_name] = $model->{$associatedModel}->find('all', $data, true);
							
						}
					}
				}
			}
		}
		
		return $results;
	}
	
	
	
	
	/**
	 * fieldBelongsToModel function.
	 * 
	 * @access public
	 * @param mixed $field
	 * @param Model $model
	 * @return void
	 */
	public function fieldBelongsToModel($field, Model $model, $saveAssociated = false) {
		if (strpos($field, '.') !== false) {
			list($extractedModel, $field) = explode('.', $field);
			if ($model->_name === $extractedModel) {
				return true;
			}
			foreach ($model->_associations as $type => $value) {
				if (is_array($value)) {
					foreach ($value as $key => $val) {
						foreach ($val as $assosiatedModel => $relationship) {
							if ($type === 'hasOne' || $type === 'belongsTo') {
								if ($extractedModel === $assosiatedModel && $saveAssociated) {
									return true;
								}
							}
						}
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * fieldQuote function.
	 * 
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function fieldQuote($field) {
		if (strpos($field, '.') !== false) {
			list($model, $field) = explode('.', $field);
			return $this->quote 
				   . trim($model) 
				   . $this->quote 
				   . '.' . $this->quote 
				   . trim($field) 
				   . $this->quote;
		} else {
			return $this->quote . trim($field) . $this->quote;
		}
	}
	
	/**
	 * placeHold function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function placeHold($value) {
		$this->_params[] = $value;
		return '?';
	}
	
	/**
	 * getLastInsertId function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getLastInsertId() {
		return $this->_connection->lastInsertId();
	}
	
	/**
	 * getAffected function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getAffected() {
		return $this->_handle->rowCount();
	}
	
	/**
	 * fetchResults function.
	 * 
	 * @access public
	 * @param bool $count (default: false)
	 * @param bool $associated (default: false)
	 * @return void
	 */
	public function fetchResults($count = false, $associated = false) {

		$columns = array();
		
		for($i = 0; $i < $this->_handle->columnCount(); $i++) {
			$meta = $this->_handle->getColumnMeta($i);
			$columns[$i] = $meta;
			
		}
		
		$result = array();
		while($row = $this->_handle->fetch(PDO::FETCH_NUM)) {
			$result[] = array();
			foreach($row as $key => $val) {
				if (!$count && !$associated)
					$result[count($result) - 1][$columns[$key]['table']][$columns[$key]['name']] = $val;
				elseif (!$count && $associated)
					$result[count($result) - 1][$columns[$key]['name']] = $val;
			}
			
		}
		if ($count) {
			return (int) $val;
		}
		$this->_handle->closeCursor();
		return $result;
	}
	
}