<?php
class View {
		public $view = 'index';
		public $layout = 'default';
		public $controller = 'Pages';
		
		protected $viewVars = array();
		
		public function renderPage() {
			$return = $this->renderLayout($this->loadView($this->viewVars));
			if($return !== false) {
				return $return;
			}
			throw new NotFoundException();
		}
		
		public function set($key, $value) {
			$this->viewVars[$key] = $value;
		}
		
		public function reset($key) {
			unset($this->viewVars[$key]);
		}
		
		public function setView($view = null) {
			if($view !== null) {
				$this->view = $view;
			}
		}
		
		public function setController($controller = null) {
			if($controller !== null) {
				$this->controller = $controller;
				
			}
		}
		
		public function viewExists($controller, $view) {
			if(file_exists(ROOT . DS  . 'View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			} elseif(file_exists(ROOT . DS . 'lib/View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			}
			return false;
		}
		
		protected function renderLayout($content = null) {
			$view = $this->_evaluate(ROOT . DS  . 'View/Layout/' . $this->layout . '.ctp', array('content' => $content));
			if($view) {
				return $view;
			} else {
				$view = $this->_evaluate(ROOT . DS  . 'lib/View/Layout/' . $this->layout . '.ctp', array('content' => $content));
				if($view !== false) {
					return $view;
				}
			}
			return false;
		}
		
		protected function loadView() {
			$view = $this->_evaluate(ROOT . DS . 'View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
			if($view !== false) {
				return $view;
			} else {
				$view = $this->_evaluate(ROOT . DS . 'lib/View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
				if($view !== false) {
					return $view;
				}
			}
		}
		
		protected function _evaluate($___viewFn, $___dataForView = null) {
			if(file_exists($___viewFn)) {
				extract($___dataForView, EXTR_SKIP);
				ob_start();
		
				include $___viewFn;
		
				return ob_get_clean();
			}
			return false;
			
		}
}