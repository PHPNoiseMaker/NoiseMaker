<?php
App::uses('PdoSource', 'Model/Datasource');
class Mysql extends PdoSource{ 
	protected $_baseConfig = array(
		'persistent' => false,
		'host' => 'localhost',
		'port' => '3306',
		'login' => 'root',
		'password' => '',
		'database' => 'ender',
	);
	
	public function connect() {
		$config = $this->_config;
		$this->connected = false;
		try {
			$flags = array(
				PDO::ATTR_PERSISTENT => $config['persistent'],
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);
			if (!empty($config['encoding'])) {
				$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['encoding'];
			}
			if (empty($config['unix_socket'])) {
				$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
			} else {
				$dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
			}
			$this->_connection = new PDO(
				$dsn,
				$config['login'],
				$config['password'],
				$flags
			);
			$this->connected = true;
		} catch (PDOException $e) {
			throw new MissingConnectionException($e->getMessage());
		}
		return $this->connected;
	}
	
	public function buildGroupBy($fields) {
		if (is_array($fields)) {
			foreach($fields as $field) {
				$this->buildGroupBy($field);
			}
		} else {
			if ($this->_groupBy === null) {
				$this->_groupBy = 'GROUP BY ' . $this->fieldQuote($fields);
			} else {
				$this->_groupBy .= ', ' . $this->fieldQuote($fields);
			}
		}
	
	}
	
	
	public function describe(Model &$model) {
		$this->prepare('DESCRIBE `' . $model->_table .'`');
		$this->execute();
		$class = get_class($model);

		$results = $this->fetchResults();
		$schema = array();
		foreach ($results as $row) {
			$row = array_shift($row);
			$field = $row['Field'];
			unset($row['Field']);
			$schema[$class][$field] = $row;
		}
		return $schema;
	}
}