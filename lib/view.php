<?php
class View {
		public $view = 'default';
		public $layout = 'default';
		public $controller = 'Homes';
		
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
		
		protected function renderLayout($content = null) {
			$view = $this->_evaluate(ROOT . DS . APP_DIR . '/view/layout/' . $this->layout . '.ctp', array('content' => $content));
			if(!empty($view) && $view) {
				return $view;
			}
			return false;
		}
		
		protected function loadView() {
			$view = $this->_evaluate(ROOT . DS . APP_DIR . '/view/' . $this->controller . '/' . $this->view . '.ctp', $this->viewVars);
			if(!empty($view)) {
				return $view;
			}
			return false;
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