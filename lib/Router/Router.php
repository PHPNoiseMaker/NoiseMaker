<?php
class Router {
	/**
	 * command
	 * Clean command (script name stripped out)
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $command = array();
	
	
	
	/**
	 * params
	 * Parameters to be passed to the Controller function
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $params = array();
	
	
	/**
	 * namedParams
	 * Named Parameters to be passed into $this->params['named'] (in Controller)
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $namedParams = array();
	
	
	/**
	 * controller
	 * Name of the requested Controller
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $controller;
	
	
	/**
	 * action
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $action;
	
	
	
	/**
	 * requestURI
	 * Raw URI
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $requestURI;
	
	
	/**
	 * scriptName
	 * Name of the script
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $scriptName;
	
	
	
	/**
	 * rules
	 * 
	 * Route rules
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $rules = array();
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	public function __construct($controller = null) {
		$this->requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$this->scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
	}
	
	/**
	 * init function.
	 *
	 * Parse the URI 
	 * 
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->_parseURI();
	}
	
	/**
	 * addRule function.
	 *
	 * See Config/Routes.php for examples
	 * 
	 * @access public
	 * @param mixed $uri
	 * @param array $target (default: array())
	 * @param array $args (default: array())
	 * @return void
	 */
	public function addRule($uri, $target = array(), $args = array()) {
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
	
	
	
	/**
	 * getCommand function.
	 * 
	 * 
	 * @access public
	 * @return $command
	 */
	public function getCommand() {
		return $this->command;
	}
	
	/**
	 * getNamedParams function.
	 * 
	 * @access public
	 * @return $namedParams
	 */
	public function getNamedParams() {
		return $this->namedParams;
	}
	
	/**
	 * getController function.
	 * 
	 * @access public
	 * @return $controller
	 */
	public function getController() {
		return $this->controller;
	}
	/**
	 * getParams function.
	 * 
	 * @access public
	 * @return $params
	 */
	public function getParams() {
		return $this->params;
	}
	/**
	 * getAction function.
	 * 
	 * @access public
	 * @return $action
	 */
	public function getAction() {
		return $this->action;
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
	
	/**
	 * arrayClean function.
	 * 
	 * @access private
	 * @param array $array
	 * @return void
	 */
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
	
	/**
	 * parseNamedParams function.
	 * 
	 * @access private
	 * @param array $params (default: array())
	 * @return array $newParams
	 */
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
	
	/**
	 * matchRule function.
	 * 
	 * @access private
	 * @param mixed $rule
	 * @return bool
	 */
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
						if($varName == 'controller') {
							$this->controller = $this->command[$i];
						} elseif ($varName == 'action') {
							$this->action = $this->command[$i];
						} elseif ($position !== false) {
							$paramsBuffer[$position] = $this->command[$i];
						}
						$this->params = $paramBuffer;
						
						
					
					} elseif(strcmp($parsedValue, $this->command[$i]) === 0) {
						
						if($this->controller === null)
							$this->controller = $this->rules[$rule]['target']['controller'];
						if($this->action === null)
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
	
	/*private function defaultRoutes() {
		$commands = $this->arrayClean($this->command);
		
		if (count($commands)) {
			$this->controller = array_shift($commands);
			$this->action = array_shift($commands);
			$this->params = $commands;
		}
	}*/
	/**
	 * _parseURI function.
	 *
	 * Parses the URI
	 * 
	 * @access private
	 * @return void
	 */
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
		
		/*if(!$matched) {
			$this->defaultRoutes();
		} */ 
		
		$this->params = $this->parseNamedParams($this->params);
		
	}
}