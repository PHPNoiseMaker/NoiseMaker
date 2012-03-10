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
	 * _detectors - Thanks to CakePHP 2.0
	 * 
     * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @link          http://cakephp.org CakePHP(tm) Project
	 * @since         CakePHP(tm) v 2.0
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 * @var mixed
	 * @access protected
	 */
	protected $_detectors = array(
		'get' => array('env' => 'REQUEST_METHOD', 'value' => 'GET'),
		'post' => array('env' => 'REQUEST_METHOD', 'value' => 'POST'),
		'put' => array('env' => 'REQUEST_METHOD', 'value' => 'PUT'),
		'delete' => array('env' => 'REQUEST_METHOD', 'value' => 'DELETE'),
		'head' => array('env' => 'REQUEST_METHOD', 'value' => 'HEAD'),
		'options' => array('env' => 'REQUEST_METHOD', 'value' => 'OPTIONS'),
		'ssl' => array('env' => 'HTTPS', 'value' => 1),
		'ajax' => array('env' => 'HTTP_X_REQUESTED_WITH', 'value' => 'XMLHttpRequest'),
		'flash' => array('env' => 'HTTP_USER_AGENT', 'pattern' => '/^(Shockwave|Adobe) Flash/'),
		'mobile' => array('env' => 'HTTP_USER_AGENT', 'options' => array(
			'Android', 'AvantGo', 'BlackBerry', 'DoCoMo', 'Fennec', 'iPod', 'iPhone', 'iPad',
			'J2ME', 'MIDP', 'NetFront', 'Nokia', 'Opera Mini', 'Opera Mobi', 'PalmOS', 'PalmSource',
			'portalmmm', 'Plucker', 'ReqwirelessWeb', 'SonyEricsson', 'Symbian', 'UP\\.Browser',
			'webOS', 'Windows CE', 'Windows Phone OS', 'Xiino'
		)),
		'requested' => array('param' => 'requested', 'value' => 1)
	);
	
	
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
	
	public function selfURL() {
		$port = env('SERVER_PORT');
		$serverName = env('SERVER_NAME');
		$requestURI = env('REQUEST_URI');
		
		if(env('HTTPS') !== false) {
			if(env('HTTPS') == 'on') {
				$s = 's';
			} else {
				$s = '';
			}
		} else {
			$s = '';
		}
		$strleft = function ($s1, $s2) {
			return substr($s1, 0, strpos($s1, $s2));
		};
		
		$protocol = $strleft(strtolower(env('SERVER_PROTOCOL')), '/') . $s;
		
		
		if($port == '80' || $port == '443') {
			$port = '';
		}
		return $protocol . '://' . $serverName . $port . $requestURI;
	}	
	
	/**
	 * is function.
	 *
	 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @link          http://cakephp.org CakePHP(tm) Project
	 * @since         CakePHP(tm) v 2.0
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 * 
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function is($type) {
		$type = strtolower($type);
		if (!isset($this->_detectors[$type])) {
			return false;
		}
		$detect = $this->_detectors[$type];
		if (isset($detect['env'])) {
			if (isset($detect['value'])) {
				return env($detect['env']) == $detect['value'];
			}
			if (isset($detect['pattern'])) {
				return (bool)preg_match($detect['pattern'], env($detect['env']));
			}
			if (isset($detect['options'])) {
				$pattern = '/' . implode('|', $detect['options']) . '/i';
				return (bool)preg_match($pattern, env($detect['env']));
			}
		}
		if (isset($detect['param'])) {
			$key = $detect['param'];
			$value = $detect['value'];
			return isset($this->params[$key]) ? $this->params[$key] == $value : false;
		}
		if (isset($detect['callback']) && is_callable($detect['callback'])) {
			return call_user_func($detect['callback'], $this);
		}
		return false;
	}
	
}