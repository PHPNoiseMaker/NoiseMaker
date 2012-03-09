<?php

/**
 * Request class.
 */
class Request {
	
	/**
	 * command
	 * 
	 * @var mixed
	 * @access public
	 */
	public $command;
	/**
	 * data
	 * 
	 * @var mixed
	 * @access public
	 */
	public $data;
	
	/**
	 * queryString
	 * 
	 * @var mixed
	 * @access public
	 */
	public $queryString;
	/**
	 * params
	 * 
	 * @var mixed
	 * @access public
	 */
	public $params;
	/**
	 * namedParams
	 * 
	 * @var mixed
	 * @access public
	 */
	public $namedParams;
	/**
	 * controller
	 * 
	 * @var mixed
	 * @access public
	 */
	public $controller;
	/**
	 * action
	 * 
	 * @var mixed
	 * @access public
	 */
	public $action;
	
	/**
	 * requestURI
	 * 
	 * @var mixed
	 * @access public
	 */
	public $requestURI;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
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
	
	/**
	 * _parseGET function.
	 * 
	 * @access private
	 * @return void
	 */
	private function _parseGET() {
		if(isset($_GET) && is_array($_GET)) {
			$this->command = array_shift($_GET);
			foreach($_GET as $key => $val) {
				$this->queryString[$key] = $val;
			}
		}
	}
	
	
	/**
	 * _parsePOST function.
	 * 
	 * @access private
	 * @return void
	 */
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
	 * getClientIP function.
	 * 
	 * @access public
	 * @return void
	 */
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