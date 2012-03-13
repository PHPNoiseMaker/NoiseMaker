<?php

class DataSource {
	
	protected $_connected = false;
	
	protected $_connection = null;
	
	protected $_config = array();
	
	protected $_baseConfig = array();
	
	public function __construct($config) {
		$this->setConfig($config);
	}
	
	private function setConfig($config) {
		$this->_config = array_merge(
			$this->_baseConfig,
			$this->_config,
			$config
		);
	}

}