<?php
class View {

		/**
		 * view
		 * 
		 * (default value: 'index')
		 * 
		 * @var string
		 * @access public
		 */
		protected $view = 'index';
		
		
		
		/**
		 * layout
		 * 
		 * (default value: 'default')
		 * 
		 * @var string
		 * @access public
		 */
		protected $layout = 'default';
		
		
		
		/**
		 * controller
		 * 
		 * (default value: 'Pages')
		 * 
		 * @var string
		 * @access public
		 */
		public $controller = 'Pages';
		
		/**
		 * viewVars
		 * 
		 * (default value: array())
		 * 
		 * @var array
		 * @access protected
		 */
		protected $viewVars = array();
		
		
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @param mixed $controller (default: null)
		 * @param mixed $view (default: null)
		 * @param mixed $viewVars (default: null)
		 * @return void
		 */
		public function __construct($controller = null, $view = null, $viewVars = null) {
			if ($controller !== null) {
				$this->controller = $controller;
			}
			if ($view !== null) {
				$this->view = $view;
			}
			if ($view !== null) {
				$this->viewVars = $viewVars;
			}
		}
		
		
		
		
		/**
		 * renderPage function.
		 * 
		 * @access public
		 * @return void
		 */
		public function renderPage() {
			try {
				$return = $this->renderLayout($this->loadView($this->viewVars));
				if ($return !== false) {
					return $return;
				}
			} catch (Exception $e) {
				throw new $e;
			}
		}

		
		/**
		 * reset function.
		 * 
		 * @access public
		 * @param mixed $key
		 * @return void
		 */
		public function reset($key) {
			unset($this->viewVars[$key]);
		}
		
		/**
		 * setView function.
		 * 
		 * @access public
		 * @param mixed $view (default: null)
		 * @return void
		 */
		public function setView($view = null) {
			if ($view !== null) {
				$this->view = $view;
			}
		}
		
		/**
		 * setController function.
		 * 
		 * @access public
		 * @param mixed $controller (default: null)
		 * @return void
		 */
		public function setController($controller = null) {
			if ($controller !== null) {
				$this->controller = $controller;
				
			}
		}
		
		/**
		 * viewExists function.
		 * 
		 * @access public
		 * @param mixed $controller
		 * @param mixed $view
		 * @return void
		 */
		public static function viewExists($controller, $view) {
			if (file_exists(ROOT . DS  . APP_DIR . DS . 'View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			} elseif (file_exists(ROOT . DS . 'lib/View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			}
			return false;
		}
		
		/**
		 * renderLayout function.
		 * 
		 * @access protected
		 * @param mixed $content (default: null)
		 * @return void
		 */
		protected function renderLayout($content = null) {
			$view = $this->_evaluate(ROOT . DS  . APP_DIR . DS . 'View/Layout/' . $this->layout . '.ctp', array('content' => $content));
			if ($view) {
				return $view;
			} else {
				$view = $this->_evaluate(ROOT . DS  . 'lib/View/Layout/' . $this->layout . '.ctp', array('content' => $content));
				if ($view !== false) {
					return $view;
				}
			}
			throw new LayoutNotFoundException();
		}
		
		/**
		 * loadView function.
		 * 
		 * @access protected
		 * @return void
		 */
		protected function loadView() {
			$view = $this->_evaluate(ROOT . DS . APP_DIR . DS . 'View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
			if ($view) {
				return $view;
			} else {
				$view = $this->_evaluate(ROOT . DS . 'lib/View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
				if ($view !== false) {
					return $view;
					
				}
			}
			throw new ViewNotFoundException();
		}
		
		/**
		 * _evaluate function.
		 * 
		 * @access protected
		 * @param mixed $___viewFn
		 * @param mixed $___dataForView (default: null)
		 * @return void
		 */
		protected function _evaluate($___viewFileName, $___viewData = null) {
			if (file_exists($___viewFileName)) {
				extract($___viewData, EXTR_SKIP);
				ob_start();
		
				include_once $___viewFileName;
				
				$contents = ob_get_contents();
				if($contents = '' || empty($contents)) {
					return ' ';
				}
				return ob_get_clean();
			}
			
			return false;
			
		}
}