<?php
require_once(ROOT . DS . APP_DIR . '/lib/router.php');
class Dispatcher {
	protected $router;
	protected $controller;
	
	public function __construct() {
		$this->router = new Router();
	}
	
	public function dispatch() {
		$class = $this->router->getController() . 'Controller';
		if(file_exists(ROOT . DS . APP_DIR . '/controllers/' . $class . '.php')) {
			include 'controllers/' . $class . '.php';
			
			$this->controller = new $class();
			
			$requestedMethod = $this->router->getMethod();
			$params = $this->router->getParams();
			
			if(method_exists($this->controller, $requestedMethod)) {
				call_user_func_array(array($this->controller, $requestedMethod), $params);
				
			} elseif( method_exists($this->controller, 'index')) {
				call_user_func_array(array($this->controller, 'index'), $params);
			}
			$this->controller->render($this->router->getController(), $requestedMethod);
	
		} elseif(file_exists(ROOT . DS . APP_DIR . '/lib/controllers/' . $class . '.php')) {
			include ROOT . DS . APP_DIR . '/lib/controllers/' . $class . '.php';
			
			$this->controller = new $class();
			$requestedMethod = $this->router->getMethod();
			$params = $this->router->getParams();
			
			if(method_exists($this->controller, $requestedMethod)) {
				call_user_func_array(array($this->controller, $requestedMethod), $params);
				
			} elseif( method_exists($this->controller, 'index')) {
				call_user_func_array(array($this->controller, 'index'), $params);
			}
			$this->controller->render($this->router->getController(), $requestedMethod);
			
		} else {
			include ROOT . DS . APP_DIR . '/lib/controllers/ErrorsController.php';
			
			$this->controller = new ErrorsController();
			$requestedURI = $this->router->getURI();
			$params = $this->router->getParams();
			
			$params['uri'] = $requestedURI;
			$this->controller->index($params);
			$this->controller->render($this->router->getController(), 'index');
		
		}
		
	}

}