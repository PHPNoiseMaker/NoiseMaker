<?php
class Router {
	protected $command = array();
	protected $params = array();
	protected $namedParams = array();
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
	
	public function addRule($uri, $target, $args = array()) {
		if(is_array($args)) {
			if(empty($args)) {
				$args['pass'] = array();
			}
		}
		$this->rules[$uri] = array(
			'target' => $target,
			'pass' => $args['pass']
		);
	}
	
	public function getCommand() {
		return $this->command;
	}
	
	public function getNamedParams() {
		return $this->namedParams;
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
	
	private function parseNamedParams($params = array()) {
		$newParams = array();
		foreach($params as $key => $val) {
			if(strpos($val, ':') !== false) {
				list($name, $value) = explode(':', $val);
				if(!empty($name) && !empty($value))
					$this->namedParams[$name] = $value;
			} else {
				$newParams[] = $val;
			}
		}
		return $newParams;
	}
	
	private function matchRule($rule) {
		$paramBuffer = array();
		$this->command = $this->arrayClean($this->command);
		$commandCount = sizeof($this->command);
		
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
						
					} elseif(
						isset($parsedRule[$parsedRuleCount])
						&& $parsedRule[$parsedRuleCount] != '*'
					) {
						return false;
					}
				}
				$i++;
			}
			
			if(
				isset($parsedRule[$parsedRuleCount])
				&& $parsedRule[$parsedRuleCount] == '*'
			) {
				for($a = $i - 1; $a <= $commandCount; $a++) {
					if(isset($this->command[$a]) && !empty($this->command[$a]))
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
		
		return $this->controller !== null ? true : false;
	}
	
	private function defaultRoutes() {
		$commands = $this->arrayClean($this->command);
		
		if (count($commands)) {
			$this->controller = array_shift($commands);
			$this->action = array_shift($commands);
			$this->params = $commands;
		}
	}
	private function _parseURI() {

		for($i= 0; $i < sizeof($this->scriptName); $i++) {
			if ($this->requestURI[$i] == $this->scriptName[$i]) {
				unset($this->requestURI[$i]);
			}
		}
		
		$this->command = array_values($this->requestURI);
		
		
		
		
		foreach($this->rules as $rule => $target) {
			$matched = $this->matchRule($rule);
			if($matched)
				break;
		}
		
		if(!$matched) {
			$this->defaultRoutes();
		}  
		
		$this->params = $this->parseNamedParams($this->params);
		
	}
}