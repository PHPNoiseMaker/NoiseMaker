<?php
require_once(ROOT . DS . APP_DIR . '/lib/view.php');
class Controller {
	
	public $view;
	
	public function __construct() {
		$this->view = new View();
	}
	
	public function render($controller = null, $view = null) {
		$this->view->setView($view);
		$this->view->setController($controller);
		echo $this->view->renderPage();
	}

}