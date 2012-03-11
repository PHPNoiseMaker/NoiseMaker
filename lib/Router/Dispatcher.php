<?php
App::uses('Router', 'Router');
App::uses('Controller', 'Controller');
App::import('Routes', 'Router');
App::uses('ObjectRegistry', 'Utility');
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
	public function __construct() {
		$this->request = ObjectRegistry::getObject('Request');
		$this->response = ObjectRegistry::getObject('Response');
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
		App::uses($class, 'Controller');
		$controllerRef = new ReflectionClass($class);
		
		if($controllerRef->isAbstract() || $controllerRef->isInterface()) {
			return false;
		}
		
		$this->controller = ObjectRegistry::storeObject($class, $controllerRef->newInstance());
		
		if($this->controller instanceof Controller) {
			return true;
		}
		return false;
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
	

		if( !$this->_loadController($class) ) {
			throw new ControllerNotFoundException();
		}
		
		
		if(method_exists($this->controller, $this->request->action)) {
			
			$ref = new ReflectionClass(get_class($this->controller));
			$method = $ref->getMethod($this->request->action);

			if($method->isPrivate()) {
				$error = 'Trying to access a private method!';
			}
			if($method->isProtected()) {
				$error = 'Trying to access a protected method!';
			}
			unset($ref, $method);
			
			if(isset($error)) {
				throw new MethodNotAllowedException($error);
			}
			
			$this->controller->view = $this->request->action;
		
			$this->controller->beforeFilter();
			
			call_user_func_array(
				array(
					$this->controller, 
					$this->request->action
				), 
				$this->request->params
			);
			$this->controller->afterFilter();
	
		} else {
		
			throw new ActionNotFoundException();
		}
		$this->controller->render($this->request->controller);		
	}

}