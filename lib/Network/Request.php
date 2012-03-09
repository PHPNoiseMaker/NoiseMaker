<?php

class Request {
	
	public $command;
	public $data;
	
	public $queryString;
	
	public $requestURI;
	
	
	public function __construct() {
		$this->requestURI = explode('/',env('REQUEST_URI'));
		$scriptName = explode('/',env('SCRIPT_NAME'));
		
		for($i= 0; $i < sizeof($scriptName); $i++) {
			if ($this->requestURI[$i] == $scriptName[$i]) {
				unset($this->requestURI[$i]);
			}
		}
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
	
	/**
	 * getURI function.
	 * 
	 * @access public
	 * @return $requestURI
	 */
	public function getURI() {
		return $this->requestURI;
	}
}