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
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @return void
	 */
	public function __construct(Request $request) {
		$this->request = $request;
		$this->params['named'] = $this->request->namedParams;
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
	 * Initialized the View
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
		echo $this->View->renderPage();
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
			header('Location: ' . $location);
		}
		return false;
	}
}