<?php

class Request {
	
	public $command;
	public $data;
	
	public $queryString;
	
	
	public function __construct() {
		$this->_parseGET();
		$this->_parsePOST();
	}
	
	private function _parseGET() {
		if(isset($_GET) && is_array($_GET)) {
			$this->command = array_shift($_GET);
			foreach($_GET as $key => $val) {
				$this->queryString[$key] = $val;
			}
		}
	}
	
	
	private function _parsePOST() {
		if(isset($_POST) && is_array($_POST)) {
			foreach($_POST as $key => $val) {
				$this->data[$key] = $val;
			}
		}
		if(isset($this->data['data'])) {
			$data = $this->data['data'];
			unset($this->data['data']);
			$this->data = array_merge($this->data, $data);
		}
	}
}