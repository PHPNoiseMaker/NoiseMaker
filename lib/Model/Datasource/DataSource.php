<?php

class DataSource {

	protected $_config = null;
	
	public function __construct($config) {
		$this->_config = $config;
	}

}