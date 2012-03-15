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
	protected static $command = array();
	
	
	
	/**
	 * params
	 * Parameters to be passed to the Controller function
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected static $params = array();
	
	
	/**
	 * namedParams
	 * Named Parameters to be passed into $this->params['named'] (in Controller)
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected static $namedParams = array();
	
	
	/**
	 * controller
	 * Name of the requested Controller
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected static $controller;
	
	
	/**
	 * action
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected static $action;
	
	
	
	/**
	 * requestURI
	 * Raw URI
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected static $requestURI;
	
	
	
	
	/**
	 * rules
	 * 
	 * Route rules
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected static $rules = array();
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	public function __construct() {
	
	}
	
	/**
	 * init function.
	 *
	 * Parse the URI 
	 * 
	 * @access public
	 * @return void
	 */
	public static function init($url) {
		self::_parseURI($url);
		self::$params = self::parseNamedParams(self::$params);
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
	public static function addRule($uri, $target = array(), $args = array()) {
		if (is_array($args)) {
			if (empty($args)) {
				$args['pass'] = array();
			}
		}
		self::$rules[$uri] = array(
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
	public static function getCommand() {
		return self::$command;
	}
	
	/**
	 * getNamedParams function.
	 * 
	 * @access public
	 * @return $namedParams
	 */
	public static function getNamedParams() {
		return self::$namedParams;
	}
	
	/**
	 * getController function.
	 * 
	 * @access public
	 * @return $controller
	 */
	public static function getController() {
		return self::$controller;
	}
	/**
	 * getParams function.
	 * 
	 * @access public
	 * @return $params
	 */
	public static function getParams() {
		return self::$params;
	}
	/**
	 * getAction function.
	 * 
	 * @access public
	 * @return $action
	 */
	public static function getAction() {
		return self::$action;
	}
	
	
	/**
	 * arrayClean function.
	 * 
	 * @access private
	 * @param array $array
	 * @return void
	 */
	private static function arrayClean($array) {
		foreach($array as $key => $value) {
			if (strlen($value) == 0) {
				unset($array[$key]);
			}
		}
		if (count($array) < 1) {
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
	private static function parseNamedParams($params = array()) {
		$newParams = array();
		foreach($params as $key => $val) {
			if (strpos($val, ':') !== false) {
				list($name, $value) = explode(':', $val);
				if (!empty($name) && !empty($value))
					self::$namedParams[$name] = $value;
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
	private static function matchRule($rule) {
		$paramBuffer = array();
		self::$command = self::arrayClean(self::$command);
		$commandCount = sizeof(self::$command);
		
		$parsedRule = self::arrayClean(explode('/', $rule));
		$parsedRuleCount = count($parsedRule);
		if (
			$parsedRuleCount == $commandCount
			|| (
				isset($parsedRule[$parsedRuleCount])
				&& $parsedRule[$parsedRuleCount] == '*'
			)
				
		) {
			
		
			$i = 0;
			foreach ($parsedRule as $parsedKey => $parsedValue) {
				
				if (isset(self::$command[$i])) {
					
					if (strpos($parsedValue, ':') === 0) {
						$varName = substr($parsedValue, 1);
						$position = array_search($varName, self::$rules[$rule]['pass']);
						if ($varName == 'controller') {
							self::$controller = self::$command[$i];
						} elseif ($varName == 'action') {
							self::$action = self::$command[$i];
						} elseif ($position !== false) {
							
							$paramBuffer[$position] = self::$command[$i];
							
						}
						
						self::$params = $paramBuffer;
					
						
					
					} elseif (strcmp($parsedValue, self::$command[$i]) === 0) {
						
						if (self::$controller === null)
							self::$controller = self::$rules[$rule]['target']['controller'];
						if (self::$action === null)
							self::$action = self::$rules[$rule]['target']['action'];
						
						foreach(self::$rules[$rule]['target'] as $ruleTargetKey => $ruleTargetVal) {
							if (
								$ruleTargetKey !== 'action'
								&& $ruleTargetKey !== 'controller'
							)
							self::$params[] = $ruleTargetVal;
						}
						
					} elseif (
						isset($parsedRule[$parsedRuleCount])
						&& $parsedRule[$parsedRuleCount] != '*'
					) {
						return false;
					}
				}
				$i++;
			}
			
			if (
				isset($parsedRule[$parsedRuleCount])
				&& $parsedRule[$parsedRuleCount] == '*'
			) {
				for($a = $i - 1; $a <= $commandCount; $a++) {
					if (isset(self::$command[$a]) && !empty(self::$command[$a]))
						self::$params[] = self::$command[$a];
				}
			}
			
			if (!$i) {
				if (
					isset(self::$rules[$rule]['target']['controller']) 
					&& isset(self::$rules[$rule]['target']['action'])
				) {
					self::$controller = self::$rules[$rule]['target']['controller'];
					self::$action = self::$rules[$rule]['target']['action'];
				}
			}
			
		}
		
		return (self::$controller !== null) ? true : false;
	}
	
	/**
	 * _parseURI function.
	 *
	 * Parses the URI
	 * 
	 * @access private
	 * @return void
	 */
	private static function _parseURI($url) {

		self::$command = array_values($url);
		
		$commandCount = count(self::$command);
			
		if (strpos(self::$command[$commandCount - 1], '?') !== false) {
			list($command) = explode('?', self::$command[$commandCount - 1]);
			self::$command[$commandCount - 1] = $command;
		}
		foreach(self::$rules as $rule => $target) {
			$matched = self::matchRule($rule);
			if ($matched)
				break;
		}
	}
}