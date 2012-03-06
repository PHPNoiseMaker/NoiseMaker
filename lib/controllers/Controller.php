<?php
require_once('lib/View/View.php');
class Controller {
	
	public $View;
	public $view = '';
	
	public function __construct() {
		$this->View = new View();
	}
	
	public function render($controller = null) {
		
		$this->View->setView($this->view);
		$this->View->setController($controller);
		echo $this->View->renderPage();
	}
	
	public function set($key, $value) {
		$this->View->set($key, $value);
	}
}