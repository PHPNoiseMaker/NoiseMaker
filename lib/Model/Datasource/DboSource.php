<?php
App::uses('DataSource', 'Model/Datasource');
class DboSource extends DataSource{ 
	protected $_handle = null;
	
	protected $_params = array();
	
	protected $_order = null;
	
	protected $_limit = null;
	
	protected $_joins = null;
	
	protected $_fields = null;
	
	protected $_table = null;
	
	protected $_alias = null;
	
	protected $_conditions = null;
	
	protected $_groupBy = null;
	
	protected $quote = '`';
	
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
				$placeHolders = $this->createPlaceHolders();
				$query = "UPDATE {$this->_table} AS {$this->_alias}"
					   . " {$this->_joins}"
					   . " SET ({$this->_fields})"
					   . " VALUES ({$placeHolders}";
				return $query;
			
			case 'insert':
				$placeHolders = $this->createPlaceHolders();
				return "INSERT INTO {$this->_table} ({$this->_fields}) VALUES ({$placeHolders}";
			
			case 'delete':
				$placeHolders = $this->createPlaceHolders();
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
	
	public function prepare($sql, $params = array()) {
		$this->_params = $params;
		$this->_handle = $this->_connection->prepare($sql);
		$this->_lastStatement = $sql;
	}
	
	public function execute($params = array()) {
		$this->_handle->execute($params);
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
		$this->_table = null;
		$this->_alias = null;
		$this->_conditions = null;
	}
	
	public function read(Model &$model, $queryData = array()) {
		$this->releaseResources();
		if (is_array($queryData)) {
			$this->_table = $model->_table;
			$this->_alias = $this->quote . $model->_name . $this->quote;
			
			if (isset($queryData['limit'])) {
				if (is_int($queryData['limit']))
					$this->_limit = 'LIMIT 0,' . $queryData['limit'];
				elseif (is_string($queryData['limit'])) {
					if (strpos($queryData['limit'], ',') !== false) {
						list($start, $end) = explode(',', $queryData['limit']);
						$this->_limit = "LIMIT {$start}, {$end}";
					} else {
						$this->_limit = 'LIMIT 0, ' . (int) $queryData['limit'];
					}
				}
			}
			
			if (isset($queryData['order'])) {
				if (is_array($queryData['order'])) {
					if ($this->_order === null) {
						$this->_order = 'ORDER BY ' . $queryData['order'][0];
					}
					for($i = 1; $i < count($queryData['order']); $i++) {
						$this->_order .= ', ' . $queryData['order'][$i];
					}
				} else {
					$this->_order = $queryData['order'];
				}
			}
			
			if (isset($queryData['fields'])) {
				
				if (is_array($queryData['fields'])) {
					$this->_fields = '';
					foreach($queryData['fields'] as $field) {
						if (!empty($field)) {
							$this->_fields .= $this->fieldQuote($field) . ',';
						}
					}

				} elseif (!empty($queryData['fields'])) {
					$this->_fields = $this->fieldQuote($queryData['fields']);
				}
				if (substr($this->_fields, strlen($this->_fields) - 1) == ',') {
					$this->_fields = substr($this->_fields, 0, strlen($this->_fields) -1);
				}
			}  else {
				$this->_fields = '*';
			}
			
			
			//Joins MUST be done before conditions. Order matters.
			
			// Joins
			
			if(isset($queryData['recursive']) && $queryData['recursive'] > -1) {
				$this->fetchAssociatedData($model, $queryData['recursive']);
			}
			
			
			
			// Conditions
			
			if (isset($queryData['conditions'])) {
				$this->_conditions = $this->parseConditions($queryData['conditions']);
			}
			
			if (isset($queryData['group'])) {
				$this->buildGroupBy($queryData['group']);
			}
			
			
			
			$sql = $this->buildStatement('select');
			$this->prepare($sql, $this->_params);
			
			return $this->fetchResults();
			
		}
		trigger_error('Query data must be an array...');
	}
	
	
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
	
	public function parseConditionArray($conditions, $params = false) {
		$out = array();
		$commands = array('AND', 'XOR', 'NOT', 'OR', '||', '&&');
	
		foreach ($conditions as $key => $value) {
			$join = ' ' . $commands[0] . ' ';
			$not = null;
	
			if (is_numeric($key) && empty($value)) {
				continue;
			} elseif (is_numeric($key) && is_string($value)) {
				$out[] = $not . $this->fieldQuote($value);
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
		} else {
			return $this->fieldQuote($key) . " = " . $value;
		}
		
	}
	
	
	public function fetchAssociatedData(Model $model, $recursive = -1) {
		foreach ($model->_associations as $association => $value) {
			foreach ($value as $key => $val) {
				foreach ($val as $associatedModel => $relationship) {
					if($association === 'hasOne') {
						$targetAlias = $model->{$associatedModel}->_name;
						$defaults = array(
							'type' => 'LEFT',
							'foreign_key' => strtolower($targetAlias) . '_id',
						);
						$settings = array_merge($defaults, $relationship);
						$joinKey = $model->_name . '.' . $settings['foreign_key'];
						$joinValue = $this->fieldQuote(
							$targetAlias 
							. '.' 
							. $model->{$associatedModel}->_primaryKey
						);
						$data = array(
							'type' => strtoupper($settings['type']),
							'table' => $this->fieldQuote($model->{$associatedModel}->_table),
							'alias' => $this->fieldQuote($targetAlias),
							'conditions' => $this->parseConditions(array(
								$joinKey => $joinValue,
							), false, false)
						);
						$this->_joins .= ' ' . $this->buildJoinStatement($data);
					}
				}	
			}	
		}
	}
	
	
	
	
	public function fieldBelongsToModel($field, $model) {
		if (strpos($field, '.') !== false) {
			list($extractedModel, $field) = explode('.', $field);
			if ($model === $extractedModel) {
				return true;
			}
		}
		return false;
	}
	
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
	
	public function placeHold($value) {
		$this->_params[] = $value;
		return '?';
	}
	
	public function fetchResults() {

		$columns = array();
		$this->_handle->execute($this->_params);
		for($i = 0; $i < $this->_handle->columnCount(); $i++) {
			$meta = $this->_handle->getColumnMeta($i);
			$columns[$i] = $meta;
			
		}
		
		$result = array();
		while($row = $this->_handle->fetch(PDO::FETCH_NUM)) {
			$result[] = array();
			foreach($row as $key => $val) {
				$result[count($result) - 1][$columns[$key]['table']][$columns[$key]['name']] = $val;
			}
			
		}
		$this->_handle->closeCursor();
		return $result;
	}
	
}