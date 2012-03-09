<?php

class Request {
	
	public $command;
	public $data;
	
	public $queryString;
	public $params;
	public $namedParams;
	public $action;
	
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
	
	public function getClientIP() {
		$fields = array(
			'HTTP_CLIENT_IP', 
			'HTTP_X_FORWARDED_FOR', 
			'HTTP_X_FORWARDED', 
			'HTTP_X_CLUSTER_CLIENT_IP', 
			'HTTP_FORWARDED_FOR', 
			'HTTP_FORWARDED', 
			'REMOTE_ADDR'
		);
	    foreach ($fields as $key) {
	        if (array_key_exists($key, $_SERVER) === true) {
	            foreach (explode(',', $_SERVER[$key]) as $ip) {
	                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
	                    return $ip;
	                }
	            }
	        }
	    }
	    return false;
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