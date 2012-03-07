<?php
require_once('lib/View/View.php');
class Controller {
	
	/**
	 * View (Class)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $View;
	
	
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
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @return void
	 */
	public function __construct(Router $router) {
		$this->View = new View();
		$this->Router = $router;
		$this->params['named'] = $this->Router->getNamedParams();
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
		
		$this->View->setView($this->view);
		$this->View->setController($controller);
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
		$this->View->set($key, $value);
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