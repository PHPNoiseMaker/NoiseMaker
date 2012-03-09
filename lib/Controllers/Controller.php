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
	 * Router
	 * Grab the instance of Router for internal use.
	 * 
	 * @var mixed
	 * @access private
	 */
	private $Router;
	
	
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
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @return void
	 */
	public function __construct(Router $router) {
		$this->Router = $router;
		$this->params['named'] = $this->Router->getNamedParams();
	}
	
	
	private function initView($controller = null, $view = null, $viewVars = null) {
		if($this->View === null) {
			$this->View = new View($controller, $view, $viewVars);
		}
	}
	
	
	/**
	 * render function.
	 * Render's the view
	 * 
	 * @access public
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	public function render($controller = null) {
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
	public function set($key, $value) {
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
	public function redirect($location) {
		if(!is_array($location)) {
			header('Location: ' . $location);
		}
		return false;
	}
}