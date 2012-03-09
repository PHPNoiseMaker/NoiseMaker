<?php
require_once('app/Controllers/AppController.php');
/**
 * ErrorsController class.
 * 
 * @extends AppController
 */
class ErrorsController extends AppController {
	/**
	 * _messages
	 * 
	 * @var mixed
	 * @access protected
	 * @static
	 */
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
	/**
	 * index function.
	 * 
	 * @access public
	 * @param mixed $params (default: null)
	 * @return void
	 */
	public function index($params = null) {
		$this->view = 'error';
		
		if(is_array($params)) {
			if(
				isset($params['error']['message'])
			) {
				
					$this->set('message', $params['error']['message']);
				
				
			} else {
				$this->set('message', '');
			}
			if(isset($params['error']['code'])) {

				header('HTTP/1.0 ' . $params['error']['code'] . ' ' . self::$_messages[$params['error']['code']]);
			} else {
				header('HTTP/1.0 ' . $params['error']['code'] . ' ' . self::$_messages[404]);
			}
		}
	}

}