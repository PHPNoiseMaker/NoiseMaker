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
	
	public function __construct(Router $router) {
		$this->View = new View();
		$this->Router = $router;
		$this->params['named'] = $this->Router->getNamedParams();
	}
	
	public function render($controller = null) {
		
		$this->View->setView($this->view);
		$this->View->setController($controller);
		echo $this->View->renderPage();
	}
	
	public function set($key, $value) {
		$this->View->set($key, $value);
	}
	
	public function redirect($location) {
		if(!is_array($location)) {
			header('Location: ' . $location);
		}
		return false;
	}
}