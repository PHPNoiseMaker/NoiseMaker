<?php
App::uses('DataSource', 'Model/Datasource');
class DboSource extends DataSource{ 
	
	public function __construct($config, $connect = true) {
		parent::__construct($config);
		if($connect) {
			$this->connect();
		}
	}
	
}