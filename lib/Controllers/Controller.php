<?php
require_once('lib/View/View.php');
class Controller {
	
	/**
	 * View (Class)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $View = null;
	
	
	/**
	 * params
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $params = array();
	
	
	/**
	 * view file to load
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	public $view = '';
	
	/**
	 * view variables to pass
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	protected $_viewVars = array();
	
	/**
	 * Instance of request
	 */
	protected $request;
	
	/**
	 * response
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $response;
	
	
	/**
	 * data
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access private
	 */
	private $data = array();  
	
		
	/**
	 * _getters
	 * 
	 * (default value: array('data'))
	 * 
	 * @var string
	 * @access private
	 */
	private $_getters = array('data');
  	/**
  	 * _setters
  	 * 
  	 * (default value: array())
  	 * 
  	 * @var array
  	 * @access private
  	 */
  	private $_setters = array();  

	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @return void
	 */
	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
		$this->params['named'] = $this->request->namedParams;
		$this->data = $request->data;
	}
	
	/**
	 * Get's current route
	 *
	 */
	
	public function getSelf() {
		$commandArray = $this->request->getURI();
		return '/' . implode('/', $commandArray);
	}
	
	
	/**
	 * Initialize the View
	 */
	
	private function initView($controller = null, $view = null, $viewVars = null) {
		if($this->View === null) {
			$this->View = new View($controller, $view, $viewVars);
		}
	}
	
	
	/**
	 * render function. reserved name
	 * Render's the view 
	 * 
	 * @access public
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	final public function render($controller = null) {
		$this->initView($controller, $this->view, $this->_viewVars);
		$this->response->buildAsset($this->View->renderPage())->send();
	}
	
	/**
	 * set function.
	 * Set View variables (wrapper function)
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	final public function set($key, $value) {
		$this->_viewVars[$key] = $value;
	}
	
	/**
	 * redirect function.
	 * Wrapper for redirection
	 * 
	 * It will accept either a url, or a path array to do a route lookup for the right URL
	 * 
	 * @access public
	 * @param mixed $location
	 * @return void
	 */
	final public function redirect($location) {
		if(!is_array($location)) {
			$this->response->setHeader(array('Location' => $location));
			$this->response->_code = 301;		
			$this->response->buildAsset();
			exit;
		}
		return false;
	}
	
	/**
	 * getModelName function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $controller
	 * @return void
	 */
	public static function getModelName($controller) {
	    if( ($pos = strpos($controller, 'Controller')) && $pos !== false) {
			return ucfirst(Inflect::singularize(substr($controller, 0, $pos)));
		}
		return false;
    }
  
	/**
	 * __get function.
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

}