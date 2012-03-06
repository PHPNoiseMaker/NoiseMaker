<?php
require_once('lib/Router/Router.php');
class Dispatcher {
	protected $router;
	protected $controller;
	
	public function __construct() {
		$this->router = new Router();
	}
	
	public function dispatch() {
		try {
			$class = $this->router->getController() . 'Controller';
			if(file_exists('Controllers/' . $class . '.php')) {
				include ROOT . DS . APP_DIR .'/Controllers/' . $class . '.php';
				
				$this->controller = new $class();
				
				$requestedMethod = $this->router->getMethod();
				$params = $this->router->getParams();
				
				if(method_exists($this->controller, $requestedMethod)) {
					call_user_func_array(array($this->controller, $requestedMethod), $params);
					
				} elseif( method_exists($this->controller, 'index')) {
					call_user_func_array(array($this->controller, 'index'), $params);
				}
				$this->controller->render($this->router->getController());
		
			} elseif(file_exists('lib/Controllers/' . $class . '.php')) {
				include 'lib/Controllers/' . $class . '.php';
				
				$this->controller = new $class();
				$requestedMethod = $this->router->getMethod();
				$params = $this->router->getParams();
				
				if(method_exists($this->controller, $requestedMethod)) {
					call_user_func_array(array($this->controller, $requestedMethod), $params);
					
				} elseif( method_exists($this->controller, 'index')) {
					call_user_func_array(array($this->controller, 'index'), $params);
				}
				$this->controller->render($this->router->getController());
				
			} else {
				include 'lib/Controllers/ErrorsController.php';
				
				$this->controller = new ErrorsController();
				$requestedURI = $this->router->getURI();
				$params = $this->router->getParams();
				
				$params['uri'] = $requestedURI;
				$this->controller->index($params);
				$this->controller->render('Errors');
			
			}
		} catch(Exception $e) {
			include 'lib/Controllers/ErrorsController.php';
				
			$this->controller = new ErrorsController();
			$requestedURI = $this->router->getURI();
			$params = array(
				'error' => array(
					'message' => $e
				)
			);
			
			$params['uri'] = $requestedURI;
			$this->controller->index($params);
			$this->controller->render('Errors');
		}
		
	}

}