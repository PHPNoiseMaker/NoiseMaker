<?php
App::uses('DataSource', 'Model/Datasource');
class DboSource extends DataSource{ 
	protected $_handle = null;
	
	public function __construct($config, $connect = true) {
		parent::__construct($config);
		if($connect) {
			$this->connect();
		}
	}
	
	public function prepare($sql) {
		$this->_handle = $this->_connection->prepare($sql);
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
	public function getColumnMeta($column) {
		return $this->_handle->getColumnMeta($column);
	}
	public function columnCount() {
		return $this->_handle->columnCount();
	}
	public function fetch() {
		return $this->_handle->fetch();
	}
	
}