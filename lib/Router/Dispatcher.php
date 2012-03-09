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
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->router = new Router();
		include_once 'lib/Router/Routes.php';
		$this->router->init();
	}
	
	/**
	 * dispatch function.
	 * 
	 * @access public
	 * @return void
	 */
	public function dispatch(Request $request) {
		try {
			$controller = $this->router->getController();
			$class = $controller . 'Controller';
			if(file_exists(ROOT . DS . APP_DIR . DS . 'Controllers/' . $class . '.php')) {
				include APP_DIR . DS . 'Controllers/' . $class . '.php';

			} elseif(file_exists(ROOT . DS . 'lib/Controllers/' . $class . '.php')) {
			
				include 'lib/Controllers/' . $class . '.php';
				
			} else {
				throw new NotFoundException();
			}
			
			$this->controller = new $class($this->router, $request);
			$requestedAction = $this->router->getAction();
			$params = $this->router->getParams();
			if(empty($requestedAction)) {
				$requestedAction = 'index';
			}
		
			$this->controller->view = $requestedAction;

			if(method_exists($this->controller, $requestedAction)) {
				try {
					call_user_func_array(
						array(
							$this->controller, 
							$requestedAction
						), 
						$params
					);
				} catch(Exception $e) {
					throw new $e;
				}
				
			} else {
			
				throw new NotFoundException();
			}
			
			
			$this->controller->render($controller);
			
		} catch(Exception $e) {
			include 'lib/Controllers/ErrorsController.php';

			$this->controller = new ErrorsController($this->router, $request);
			$requestedURI = $this->router->getURI();
			
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