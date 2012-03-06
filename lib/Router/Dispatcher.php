<?php
require_once('lib/Router/Router.php');
require_once('lib/Core/Exceptions.php');
class Dispatcher {
	protected $router;
	protected $controller;
	
	public function __construct() {
		$this->router = new Router();
	}
	
	public function dispatch() {
		try {
			$class = $this->router->getController() . 'Controller';
			if(file_exists(ROOT . DS . 'Controllers/' . $class . '.php')) {
				include 'Controllers/' . $class . '.php';
		
			} elseif(file_exists(ROOT . DS . 'lib/Controllers/' . $class . '.php')) {
				include 'lib/Controllers/' . $class . '.php';
				
			} else {
				throw new NotFoundException();
			}
			
			$this->controller = new $class();
			$requestedAction = $this->router->getAction();
			$params = $this->router->getParams();
			var_dump('', $requestedAction, $params, $this->router->getController());
			if(empty($requestedAction)) {
				$requestedAction = 'index';
			}
		
			
			if(method_exists($this->controller, $requestedAction)) {
				call_user_func_array(array($this->controller, $requestedAction), $params);
				
			} else {
				throw new NotFoundException();
			}
			
			$this->controller->render($this->router->getController());
			
		} catch(Exception $e) {
			include 'lib/Controllers/ErrorsController.php';
				
			$this->controller = new ErrorsController();
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