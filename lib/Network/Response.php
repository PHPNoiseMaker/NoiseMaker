<?php
class Response {
	private $_getters = array('_messages');
  	private $_setters = array();
  	private $_headers = array();
  	private $_body;

	protected static $_messages = array(
	    // Informational 1xx
	    100 => 'Continue',
	    101 => 'Switching Protocols',
	
	    // Success 2xx
	    200 => 'OK',
	    201 => 'Created',
	    202 => 'Accepted',
	    203 => 'Non-Authoritative Information',
	    204 => 'No Content',
	    205 => 'Reset Content',
	    206 => 'Partial Content',
	
	    // Redirection 3xx
	    300 => 'Multiple Choices',
	    301 => 'Moved Permanently',
	    302 => 'Found',  // 1.1
	    303 => 'See Other',
	    304 => 'Not Modified',
	    305 => 'Use Proxy',
	    // 306 is deprecated but reserved
	    307 => 'Temporary Redirect',
	
	    // Client Error 4xx
	    400 => 'Bad Request',
	    401 => 'Unauthorized',
	    402 => 'Payment Required',
	    403 => 'Forbidden',
	    404 => 'Not Found',
	    405 => 'Method Not Allowed',
	    406 => 'Not Acceptable',
	    407 => 'Proxy Authentication Required',
	    408 => 'Request Timeout',
	    409 => 'Conflict',
	    410 => 'Gone',
	    411 => 'Length Required',
	    412 => 'Precondition Failed',
	    413 => 'Request Entity Too Large',
	    414 => 'Request-URI Too Long',
	    415 => 'Unsupported Media Type',
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',
	
	    // Server Error 5xx
	    500 => 'Internal Server Error',
	    501 => 'Not Implemented',
	    502 => 'Bad Gateway',
	    503 => 'Service Unavailable',
	    504 => 'Gateway Timeout',
	    505 => 'HTTP Version Not Supported',
	    509 => 'Bandwidth Limit Exceeded'
	);
	
	public function __construct() {
		ob_start();
	}
	
	public function buildAsset($body) {
		if(is_array($this->_headers)) {
			foreach($this->_headers as $header) {
				header($header);
			}
		}
		echo $body;
	}
	
	public function __destruct() {
		ob_end_flush();
	}
	
	public function __get($property) {
		
	    if (in_array($property, $this->_getters)) {
	    	return $this->$property;
	    } else if (method_exists($this, '_get_' . $property)) {
	    	return call_user_func(array($this, '_get_' . $property));
	    } else if (
		    in_array($property, $this->_setters) 
		    || method_exists($this, '_set_' . $property)
	    ) {
	    	throw new InternalErrorException('Property "' . $property . '" is write-only.');
	    } else {
	    	throw new InternalErrorException('Property "' . $property . '" is not accessible.');
	    }
	}

	public function __set($property, $value) {
		if (in_array($property, $this->_setters)) {
			$this->$property = $value;
		} else if (method_exists($this, '_set_' . $property)) {
			call_user_func(array($this, '_set_' . $property), $value);
		} else if (
			in_array($property, $this->_getters) 
			|| method_exists($this, '_get_' . $property)
		) {
		  throw new InternalErrorException('Property "' . $property . '" is read-only.');
		} else {
		  throw new InternalErrorException('Property "' . $property . '" is not accessible.');
		}
	}
	public function sendCode($code = null) {
		if($code !== null) {
			if(array_key_exists($code, $this->_messages)) {
				$this->_setHeader('HTTP/1.0 ' . $code . ' ' . $this->_messages[$code]);
			} else {
				$this->_setHeader('HTTP/1.0 ' . $code . ' ' . $this->_messages[404]);
			}
		} else {
			$this->_setHeader('HTTP/1.0 404 ' . $this->_messages[404]);
		}
	
	}
	
	private function _setHeader($string) {
		$this->_headers[] = $string;
	}
}