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
	
	public function addRule($uri, $target, $pass = array()) {
		$this->rules[$uri] = array(
			'target' => $target,
			'pass' => $pass
		);
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
		if(count($array) < 1) {
			$array = array();
		}
		return $array; 
	}
	private function matchRules($rule) {
		$matched = false;
		$paramBuffer = array();
		$this->command = $this->arrayClean($this->command);
		$commandCount = count($this->command);
		
		$parsedRule = $this->arrayClean(explode('/', $rule));
		$parsedRuleCount = count($parsedRule);
		if(
			$parsedRuleCount == $commandCount
			|| (
				isset($parsedRule[$parsedRuleCount])
				&& $parsedRule[$parsedRuleCount] == '*'
			)
				
		) {
			
		
			$i = 0;
			foreach ($parsedRule as $parsedKey => $parsedValue) {
				
				if(isset($this->command[$i])) {
					
					if(strpos($parsedValue, ':') === 0) {
						$varName = substr($parsedValue, 1);
						$position = array_search($varName, $this->rules[$rule]['pass']);
						if($position !== false) {
							$paramsBuffer[$position] = $this->command[$i];
						}
						$this->params = $paramsBuffer;
						
						$matched = true;
						
					
					} elseif(strcmp($parsedValue, $this->command[$i]) === 0) {
						
						$this->controller = $this->rules[$rule]['target']['controller'];
						$this->action = $this->rules[$rule]['target']['action'];
						
						foreach($this->rules[$rule]['target'] as $ruleTargetKey => $ruleTargetVal) {
							if(
								$ruleTargetKey !== 'action'
								&& $ruleTargetKey !== 'controller'
							)
							$this->params[] = $ruleTargetVal;
						}
						$matched = true;
						
					}
				}
				$i++;
			}
			if(
				isset($parsedRule[$parsedRuleCount])
				&& $parsedRule[$parsedRuleCount] == '*'
			) {
				for($a = $i - 1; $a < $commandCount; $a++) {
					$this->params[] = $this->command[$a];
				}
			}
			
			if(!$i) {
				if(
					isset($this->rules[$rule]['target']['controller']) 
					&& isset($this->rules[$rule]['target']['action'])
				) {
					$this->controller = $this->rules[$rule]['target']['controller'];
					$this->action = $this->rules[$rule]['target']['action'];
				}
			}
		}
		
	return $matched;
	}
	private function _parseURI() {

		for($i= 0; $i < sizeof($this->scriptName); $i++) {
			if ($this->requestURI[$i] == $this->scriptName[$i]) {
				unset($this->requestURI[$i]);
			}
		}
		
		$this->command = array_values($this->requestURI);
		
		
		
		
		foreach($this->rules as $rule => $target) {
			$matched = $this->matchRules($rule);
			if($matched)
				break;
		}
		
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
				
			} elseif ( isset($this->command[0]) && !empty($this->command[0]) ) {
				$this->controller = $this->command[0];
			}
		
		}  
		
		if(empty($this->controller)) {
			//$this->controller = 'Pages';
		}
	}
}