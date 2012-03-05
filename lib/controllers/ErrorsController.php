<?php
require_once(ROOT . DS . APP_DIR . '/lib/controller.php');
class ErrorsController extends Controller {
	
	public function index($arg = null) {
		$this->view->view = 'error';
	}

}