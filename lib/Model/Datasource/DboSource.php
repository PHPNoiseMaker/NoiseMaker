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
		}
		$this->_handle->setFetchMode($fetch);
	}
	
	public function fetch() {
		return $this->_handle->fetch();
	}
	
}