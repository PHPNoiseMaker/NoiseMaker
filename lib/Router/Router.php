<?php
class Router {
	protected $command = array();
	protected $params = array();
	protected $controller;
	protected $action;
	protected $requestURI;
	protected $scriptName;
	protected $rules = array();
	
	public function __construct($controller = null) {
		$this->requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$this->scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
	}
	
	public function init() {
		$this->_parseURI();
	}
	
	public function addRule($uri, $target) {
		$this->rules[$uri] = $target;
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
	public function getAction() {
		return $this->action;
	}
	public function getURI() {
		return $this->requestURI;
	}
	private function arrayClean($array) {
		foreach($array as $key => $value) {
			if (strlen($value) == 0) {
				unset($array[$key]);
			}
		}
		return $array; 
	}
	private function matchURL() {
		$this->command = $this->arrayClean($this->command);
		$commandCount = count($this->command);
		foreach($this->rules as $ruleKey => $ruleTarget) {
			$parsedRule = $this->arrayClean(explode('/', $ruleKey));
			$parsedRuleCount = count($parsedRule);
			//var_dump('cmd',$parsedRule, $this->command, $parsedRuleCount, $commandCount);
			if($parsedRuleCount == $commandCount) {
				
				$i = 0;
				foreach ($parsedRule as $parsedKey => $parsedValue) {
					if(strcmp($parsedValue, $this->command[$i]) == 0) {
						$this->controller = $ruleTarget['controller'];
						$this->action = $ruleTarget['action'];
						return true;
					}
					$i++;
				}
			}
		}
		return false;
	}
	private function _parseURI() {

		for($i= 0; $i < sizeof($this->scriptName); $i++) {
			if ($this->requestURI[$i] == $this->scriptName[$i]) {
				unset($this->requestURI[$i]);
			}
		}
		
		$this->command = array_values($this->requestURI);
		
		//var_dump($this->command);
		
		
		
		$matched = $this->matchURL();
		
		var_dump('matched',$matched);
		if(!$matched) {
			if(count($this->command) > 1) {
				$this->controller = $this->command[0];
				$this->action = $this->command[1];
				for($i = 2; $i < count($this->command); $i++) {
					if(!empty($this->command[$i]))
						$this->params[] = $this->command[$i];
					$this->controller = $this->command[0];
				}
			} elseif(count($this->command) == 1) {
				$this->controller = $this->command[0];
				$this->params[] = $this->command[0];
				$this->controller = $this->command[0];
					
				
			} elseif ( isset($this->command[0]) && !empty($this->command[0]) ) {
				$this->controller = $this->command[0];
			}
		
		}  
		if(empty($this->controller)) {
			$this->controller = 'Pages';
		}
	}
}