<?php

class Dispatcher {
	
	public static function dispatch($router) {
		$class = $router->getController() . 'Controller';
		if(file_exists(ROOT . DS . APP_DIR . '/controllers/' . $class . '.php')) {
			include 'controllers/' . $class . '.php';
			
			$controller = new $class();
			$requestedMethod = $router->getMethod();
			$params = $router->getParams();
			
			if(method_exists($controller, $requestedMethod)) {
				call_user_func_array(array($controller, $requestedMethod), $params);
				
			} elseif( method_exists($controller, 'index')) {
				call_user_func_array(array($controller, 'index'), $params);
			}
			
			
			
		} elseif(file_exists(ROOT . DS . APP_DIR . '/lib/controllers/' . $class . '.php')) {
			include ROOT . DS . APP_DIR . '/lib/controllers/' . $class . '.php';
			
			$controller = new $class();
			$requestedMethod = $router->getMethod();
			$params = $router->getParams();
			
			if(method_exists($controller, $requestedMethod)) {
				call_user_func_array(array($controller, $requestedMethod), $params);
				
			} elseif( method_exists($controller, 'index')) {
				call_user_func_array(array($controller, 'index'), $params);
			}
			
		} else {
			include ROOT . DS . APP_DIR . '/lib/controllers/ErrorsController.php';
			
			$controller = new ErrorsController();
			$requestedURI = $router->getURI();
			$params = $router->getParams();
			
			$params['uri'] = $requestedURI;
			$controller->view->controller = 'Errors';
			$controller->lerror($params);
		
		}
		echo $controller->view->renderPage();
	}

}