<?php
require_once('lib/View/View.php');
class Controller {
	
	public $view;
	public $viewFile = 'index';
	
	public function __construct() {
		$this->view = new View();
	}
	
	public function render($controller = null) {
		$this->view->setView($this->viewFile);
		$this->view->setController($controller);
		echo $this->view->renderPage();
	}

}