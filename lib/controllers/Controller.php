<?php
require_once('lib/View/View.php');
class Controller {
	
	protected $View;
	protected $params = array();
	public $view = '';
	
	public function __construct($namedParams = array()) {
		$this->View = new View();
		$this->params['named'] = $namedParams;
	}
	
	public function render($controller = null) {
		
		$this->View->setView($this->view);
		$this->View->setController($controller);
		echo $this->View->renderPage();
	}
	
	public function set($key, $value) {
		$this->View->set($key, $value);
	}
	
	public function redirect($location) {
		if(!is_array($location)) {
			header('Location: ' . $location);
		}
		return false;
	}
}