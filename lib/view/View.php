<?php
class View {
		public $view = 'default';
		public $layout = 'default';
		public $controller = 'Pages';
		
		protected $viewVars = array();
		
		public function renderPage() {
			return $this->renderLayout($this->loadView($this->viewVars));
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
			if(file_exists('View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			} elseif(file_exists('lib/View/' . $controller . '/' . $view . '.ctp')) {
				return true;
			}
			return false;
		}
		
		protected function renderLayout($content = null) {
			$view = $this->_evaluate('View/Layout/' . $this->layout . '.ctp', array('content' => $content));
			if($view) {
				return $view;
			} else {
				$view = $this->_evaluate('lib/View/Layout/' . $this->layout . '.ctp', array('content' => $content));
				if($view !== false) {
					return $view;
				}
			}
			return false;
		}
		
		protected function loadView() {
			$view = $this->_evaluate('View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
			if($view !== false) {
				return $view;
			} else {
				$view = $this->_evaluate('lib/View/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
				if($view !== false) {
					return $view;
				}
			}
			
			return false;
		}
		
		protected function _evaluate($___viewFn, $___dataForView = null) {

			if(file_exists(ROOT . DS . $___viewFn)) {
				extract($___dataForView, EXTR_SKIP);
				ob_start();
		
				include $___viewFn;
		
				return ob_get_clean();
			}
			return false;
			
		}
}