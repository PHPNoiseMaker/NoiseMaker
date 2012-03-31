<?php
App::uses('View', 'View');
class Controller {
	
	/**
	 * name
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access public
	 */
	public $name = null;
	
	
	
	/**
	 * uses
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $uses = array();
	
	/**
	 * helpers
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $helpers = array();
	
	/**
	 * components
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $components = array();
	
	
	/**
	 * View (Class)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $View = null;
	
	
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
	 * view file to load
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	public $view = '';
	
	/**
	 * view variables to pass
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	protected $_viewVars = array();
	
	/**
	 * Instance of request
	 */
	protected $request;
	
	/**
	 * response
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $response;
	
	
	/**
	 * data
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access private
	 */
	private $data = array();  
	
		
	/**
	 * _getters
	 * 
	 * (default value: array('data'))
	 * 
	 * @var string
	 * @access private
	 */
	private $_getters = array('data');
  	/**
  	 * _setters
  	 * 
  	 * (default value: array())
  	 * 
  	 * @var array
  	 * @access private
  	 */
  	private $_setters = array(); 
  	
  	/**
  	 * _parent
  	 * 
  	 * (default value: 'AppController')
  	 * 
  	 * @var string
  	 * @access protected
  	 */
  	protected $_parent = 'AppController';

	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param Router $router
	 * @return void
	 */
	public function __construct(Request $request, Response $response) {
		if ($this->name === null) {
			$class = get_class($this);
			$this->name = substr($class, 0, strpos($class, 'Controller'));
			unset($class);
		}
		$this->request = $request;
		$this->response = $response;
		$this->params['named'] = $this->request->namedParams;
		$this->data = $this->request->data;
		$this->_mergeVars();
		$this->_constructModels();
	}
	/**
	 * __get function.
	 * 
	 * @access public
	 * @param mixed $property
	 * @return void
	 */
	public function __get($property) {
		
	    if (in_array($property, $this->_getters)) {
	    	return $this->$property;
	    } else if (method_exists($this, '_get_' . $property)) {
	    	return call_user_func(array($this, '_get_' . $property));
	    } else if (
		    in_array($property, $this->_setters) 
		    || method_exists($this, '_set_' . $property)
	    ) {
	    	throw new InternalErrorException('Property "' . $property . '" is write-only.');
	    } else {
	    	throw new InternalErrorException('Property "' . $property . '" is not accessible.');
	    }
	}

	/**
	 * __set function.
	 * 
	 * @access public
	 * @param mixed $property
	 * @param mixed $value
	 * @return void
	 */
	public function __set($property, $value) {
		if (in_array($property, $this->_setters)) {
			$this->{$property} = $value;
		} else if (method_exists($this, '_set_' . $property)) {
			call_user_func(array($this, '_set_' . $property), $value);
		} else if (
			in_array($property, $this->_getters) 
			|| method_exists($this, '_get_' . $property)
		) {
		  throw new InternalErrorException('Property "' . $property . '" is read-only.');
		} else {
		  //throw new InternalErrorException('Property "' . $property . '" is not accessible.');
		}
	}
	
	private function _mergeVars() {
		if (is_subclass_of($this, $this->_parent)) {
			$parentVars = get_class_vars($this->_parent);
			foreach ($parentVars as $var => $value) {
				if (isset($this->{$var})) {
					if (
						$var == 'components'
						|| $var == 'uses'
						|| $var == 'helpers'
					) {
						if ($this->{$var} !== false) {
							if (is_array($this->{$var}) && is_array($value)) {
								$difference = array_diff($value, $this->{$var});
								$this->{$var} = array_merge($difference, $this->{$var});
							} elseif (is_array($this->{$var}) && is_string($value)) {
								if (!in_array($value, $this->{$var})) {
									array_unshift($this->{$var}, $value);
								}
							} elseif (is_string($this->{$var}) && is_array($value)) {
								if (!in_array($this->{$var}, $value)) {
									$difference = array_diff($value, array($this->{$var}));
									$this->{$var} = array_merge(array($this->{$var}), $difference);
								}
							} else {
								$this->{$var} = array($this->{$var});
								$value = array($value);
								$difference = array_diff($value, $this->{$var});
								$this->{$var} = array_merget($this->{$var}, $value);
							}
						}
					}
				}
				
			}
		}
	}
	
	/**
	 * constructModels function.
	 * 
	 * @access private
	 * @return void
	 */
	private function _constructModels() {
		if ($this->uses !== false) {
			if (is_array($this->uses)) {
				if (sizeof($this->uses) === 0) {
					$this->uses[] = $this->getModelName();
				}
				foreach($this->uses as $model) {
					if (!$this->_loadModel($this->getModelName($model))) {
						trigger_error('Could not load the model!');
					}
				}
			} else {
				trigger_error('$this->uses must be an array!');
			}
		}
		foreach (array('hasOne', 'hasMany', 'hasAndBelongsToMany', 'belongsTo') as $type) {
			$model = $this->getModelName();
			if (isset($this->{$model})){
				if (isset($this->{$model}->{$type})) {
					if (is_array($this->{$model}->{$type})) {
						foreach ($this->{$model}->{$type} as $key => $association) {
							if (is_numeric($key) && is_string($association)) {
								$this->_loadModel($association);
							} elseif (is_string($key) && is_array($association)) {
								$this->_loadModel($key);
							}
						}
					}
				}
			}
		}
	}
	 /**
     * _loadModel function.
     * 
     * @access private
     * @param mixed $class
     * @return void
     */
    private function _loadModel($class) {

		App::uses('ObjectRegistry', 'Utility');
		$this->_setters[] = $class;
		$this->{$class} = ObjectRegistry::init($class);
		
		
		if (($key = array_search($class, $this->_setters)) !== false) {
			unset($this->_setters[$key]);
		}
		
		if ($this->{$class} instanceof Model) {
			return true;
		}
		return false;
	}
	
	
	/**
	 * Get's current route
	 *
	 */
	
	public function getSelf() {
		$commandArray = $this->request->getURI();
		return '/' . implode('/', $commandArray);
	}
	
	
	/**
	 * Initialize the View
	 */
	
	private function initView($controller = null, $view = null, $viewVars = null) {
		if ($this->View === null) {
			$this->View = new View($controller, $view, $viewVars);
		}
	}
	
	
	/**
	 * render function. reserved name
	 * Render's the view 
	 * 
	 * @access public
	 * @param mixed $controller (default: null)
	 * @return void
	 */
	final public function render($controller = null) {
		$this->initView($controller, $this->view, $this->_viewVars);
		$this->response->buildAsset($this->View->renderPage())->send();
	}
	
	/**
	 * set function.
	 * Set View variables (wrapper function)
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	final public function set($key, $value) {
		$this->_viewVars[$key] = $value;
	}
	
	/**
	 * Redirect to another URL, stop script execution.
	 * 
	 * 
	 * It will accept either a url, or a path array to do a route lookup for the right URL
	 * 
	 * @access public
	 * @param mixed $location
	 * @return void
	 */
	final public function redirect($location) {
		if (!is_array($location)) {
			$this->response->setHeader(array('Location' => $location));
			$this->response->_code = 301;		
			$this->response->buildAsset();
			exit;
		}
		return false;
	}
	
	/**
	 * getModelName function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $controller
	 * @return void
	 */
	final public function getModelName($controller = null) {
		if (is_object($controller)) {
			$controllerName = $controller->name;
		}
		if (is_string($controller)) {
		    $controllerName = $controller;
		}
		if ($controller === null) {
			$controllerName = $this->name;
		}
		if ( ($pos = strpos($controllerName, 'Controller')) && $pos !== false) {
			return ucfirst(Inflect::singularize(substr($controllerName, 0, $pos)));
		} else {
			return ucfirst(Inflect::singularize($controllerName));
		}
		return false;
    }
    
   
  
	
	/**
	 * beforeFilter function.
	 * 
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
	}
	
	/**
	 * afterFilter function.
	 * 
	 * @access public
	 * @return void
	 */
	public function afterFilter() {
	}
}