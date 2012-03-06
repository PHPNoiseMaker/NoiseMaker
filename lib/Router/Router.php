<?php
class Router {
	protected $command = array();
	protected $params = array();
	protected $controller = 'Pages';
	protected $method = 'index';
	protected $requestURI;
	protected $scriptName;
	
	public function __construct($controller = null) {
		$this->requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$this->scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
		$this->_parseURI();
		if($controller !== null) {
			$this->controller = $controller;
		}	
	}
	public function getCommand() {
		return $this->command;
	}
	
	public function getController() {
		return $this->controller;
	}
	public function getParams() {
		return $this->params;
	}
	public function getMethod() {
		return $this->method;
	}
	public function getURI() {
		return $this->requestURI;
	}
	private function _parseURI() {
		for($i= 0; $i < sizeof($this->scriptName); $i++) {
			if ($this->requestURI[$i] == $this->scriptName[$i]) {
				unset($this->requestURI[$i]);
			}
		}
		
		$this->command = array_values($this->requestURI);
		
		if(count($this->command) > 1) {
			$this->controller = $this->command[0];
			for($i = 1; $i < count($this->command); $i++) {
				if(!empty($this->command[$i]))
					$this->params[$i] = $this->command[$i];
				$this->controller = $this->command[0];
			}
		} elseif ( isset($this->command[0]) && !empty($this->command[0]) ) {
			$this->controller = $this->command[0];
		}
	}
}