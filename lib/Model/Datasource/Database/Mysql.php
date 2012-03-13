<?php
App::uses('DboSource', 'Model/Datasource');
class Mysql extends DboSource{ 
	protected $_baseConfig = array(
		'persistent' => true,
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
	
	
	public function describe(Model &$model) {
		$DBH = $this->_connection->query('DESCRIBE `' . $model->_table . '`');
		$DBH->setFetchMode(PDO::FETCH_ASSOC);
		$class = get_class($model);
		$schema = array(
			$class => array()
		);
		while($row = $DBH->fetch()) {
			$field = array_shift($row);
			$schema[$class][$field] = $row;
		}
		return $schema;
	}
}