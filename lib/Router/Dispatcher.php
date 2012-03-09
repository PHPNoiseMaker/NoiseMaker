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
	
	
	private $request;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(Request $request) {
		$this->request = $request;
		$this->router = new Router($request);
		include_once 'lib/Router/Routes.php';
		$this->router->init($this->request->getURI());
		$this->request->controller = $this->router->getController();
		$this->request->action = $this->router->getAction();
		$this->request->params = $this->router->getParams();
		$this->request->namedParams = $this->router->getNamedParams();
	}
	
	private function _loadController($class) {
		if(file_exists(ROOT . DS . APP_DIR . DS . 'Controllers/' . $class . '.php')) {
			include APP_DIR . DS . 'Controllers/' . $class . '.php';

		} elseif(file_exists(ROOT . DS . 'lib/Controllers/' . $class . '.php')) {
		
			include 'lib/Controllers/' . $class . '.php';
			
		} else {
			throw new NotFoundException();
		}
		
		$this->controller = new $class($this->request);	
	}
	
	/**
	 * dispatch function.
	 * 
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		try {
			
			$class = $this->request->controller . 'Controller';

			if(empty($this->request->action)) {
				$this->request->action = 'index';
			}
		
			$this->controller->view = $this->request->action;
			try {
				$this->_loadController($class);
			} catch(Exception $e) {
				throw new $e;
			}
			
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
			
		} catch(Exception $e) {
			//include 'lib/Controllers/ErrorsController.php';

			//$this->controller = new ErrorsController($this->router, $this->request);
			try {
				$this->_loadController('ErrorsController');
			} catch(Exception $e) {
			}
			$requestedURI = $this->request->getURI();
			
			$params = array(
				'error' => array(
					'message' => $e->getMessage(),
					'code' => $e->getCode(),
					'uri' =>  $requestedURI
				)
			);
			$this->controller->index($params);
			$this->controller->render('Errors');
		}
		
	}

}