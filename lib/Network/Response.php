<?php
/**
 * Response class.
 */
class Response {
	/**
	 * _getters
	 * Properties allowed to be read
	 * 
	 * (default value: array('_messages', '_code'))
	 * 
	 * @var string
	 * @access private
	 */
	private $_getters = array('_messages', '_code');
  	/**
  	 * _setters
  	 * 
  	 *	Properties allowed to be set
  	 *
  	 * (default value: array('_code'))
  	 * 
  	 * @var string
  	 * @access private
  	 */
  	private $_setters = array('_code');
  	
  	
  	/**
  	 * _headers - array of headers to send
  	 *
  	 * If the key is numeric it will send the value as the header string, otherwise
  	 * it will send it in the form "$key: $value"
  	 * 
  	 * (default value: array())
  	 * 
  	 * @var array
  	 * @access private
  	 */
  	private $_headers = array();
  	
  	
  	/**
  	 * _code -  The status code to send to the client
  	 * 
  	 * (default value: 200)
  	 * 
  	 * @var int
  	 * @access private
  	 */
  	private $_code = 200;
  	
  	/**
  	 * _body - The content of the asset
  	 * 
  	 * @var mixed
  	 * @access private
  	 */
  	private $_body;

	/**
	 * _messages - Status code definitions
	 * 
	 * @var mixed
	 * @access protected
	 * @static
	 */
	protected $_messages = array(
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
	
	/**
	 * __construct function.
	 *
	 * Start output buffering
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		ob_start();
	}
	
	/**
	 * buildAsset function.
	 *
	 *	Build the response to send back to the client
	 * 
	 * @access public
	 * @param mixed $body
	 * @return void
	 */
	public function buildAsset($body) {
		$this->_sendCode($this->_code);
		if(is_array($this->_headers)) {
			foreach($this->_headers as $key => $value) {
				if(is_int($key)) {
					header($value);
				} else {
					header($key . ': ' . $value);
				}
			}
		}
		$this->_body = $body;
		return $this;
	}
	
	
	/**
	 * send function.
	 * 
	 * @access public
	 * @return void
	 */
	public function send() {
		echo $this->_body;
		ob_end_flush();
	}

	
	/**
	 * __get function.
	 *
	 * Overloading get function
	 * 
	 * @access public
	 * @param mixed $property
	 * @return void
	 */
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

	/**
	 * __set function.
	 *	Overloading set function
	 * 
	 * @access public
	 * @param mixed $property
	 * @param mixed $value
	 * @return void
	 */
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
	/**
	 * _sendCode function.
	 *
	 *	set the header
	 * 
	 * @access private
	 * @param mixed $code (default: null)
	 * @return void
	 */
	private function _sendCode($code = null) {
		if($code !== null) {
			if(array_key_exists($code, $this->_messages)) {
				$this->setHeader('HTTP/1.0 ' . $code . ' ' . $this->_messages[$code]);
			} else {
				$this->setHeader('HTTP/1.0 ' . $code . ' ' . $this->_messages[404]);
			}
		} else {
			$this->setHeader('HTTP/1.0 404 ' . $this->_messages[404]);
		}
	
	}
	
	/**
	 * _setHeader function.
	 *
	 *	add headers to the array
	 * 
	 * @access private
	 * @param mixed $string
	 * @return void
	 */
	public function setHeader($header) {
		if(!is_array($header)) {
			$this->_headers[] = $header;
		} else {
			foreach($header as $key => $val) {
				$this->_headers[$key] = $val;
			}
		}
	}
}