<?php
App::import('Router', 'Router');
App::import('Exceptions', 'Core');

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
	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
		App::import('Routes', 'Router');
		Router::init($this->request->getURI());
		$this->request->controller = Router::getController();
		$this->request->action = Router::getAction();
		$this->request->params = Router::getParams();
		$this->request->namedParams = Router::getNamedParams();
	}
	
	/**
	 * _loadController function.
	 * 
	 * @access private
	 * @param mixed $class
	 * @return void
	 */
	private function _loadController($class) {
		App::import($class, 'Controllers');
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
		
		
		if(method_exists($this->controller, $this->request->action)) {
			try {
				call_user_func_array(
					array(
						$this->controller, 
						$this->request->action
					), 
					$this->request->params
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