<?php
require_once('lib/Router/Router.php');
require_once('lib/Core/Exceptions.php');
/**
 * Dispatcher class.
 */
class Dispatcher {
	/**
	 * router
	 * Instance or router that will eventually be passed to Controller
	 * 
	 * @var mixed
	 * @access private
	 */
	private $router;
	/**
	 * controller
	 * Instance of the Controller Class
	 * 
	 * @var mixed
	 * @access private
	 */
	private $controller;
	
	
	/**
	 * request
	 * 
	 * @var mixed
	 * @access private
	 */
	private $request;
	
	/**
	 * response
	 * 
	 * @var mixed
	 * @access private
	 */
	private $response;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(Request &$request, Response &$response) {
		$this->request = $request;
		$this->response = $response;
		$this->router = new Router(&$this->request);
		include_once 'lib/Router/Routes.php';
		$this->router->init($this->request->getURI());
		$this->request->controller = $this->router->getController();
		$this->request->action = $this->router->getAction();
		$this->request->params = $this->router->getParams();
		$this->request->namedParams = $this->router->getNamedParams();
	}
	
	/**
	 * _loadController function.
	 * 
	 * @access private
	 * @param mixed $class
	 * @return void
	 */
	private function _loadController($class) {
		if(file_exists(ROOT . DS . APP_DIR . DS . 'Controllers/' . $class . '.php')) {
			include APP_DIR . DS . 'Controllers/' . $class . '.php';

		} elseif(file_exists(ROOT . DS . 'lib/Controllers/' . $class . '.php')) {
		
			include 'lib/Controllers/' . $class . '.php';
			
		} else {
			throw new NotFoundException();
		}
		
		$this->controller = new $class($this->request, $this->response);	
	}
	
	/**
	 * dispatch function.
	 * 
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		
		$class = $this->request->controller . 'Controller';

		if(empty($this->request->action)) {
			$this->request->action = 'index';
		}
	

		$this->_loadController($class);
		$this->controller->view = $this->request->action;
		
		
		if(method_exists(&$this->controller, &$this->request->action)) {
			try {
				call_user_func_array(
					array(
						&$this->controller, 
						&$this->request->action
					), 
					&$this->request->params
				);
			} catch(Exception $e) {
				throw new $e;
			}
			
		} else {
		
			throw new NotFoundException();
		}
		
		$this->controller->render($this->request->controller);
		
		
	}

}