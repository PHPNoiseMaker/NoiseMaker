<?php
require_once(ROOT . DS . APP_DIR . '/lib/controller.php');
class ErrorsController extends Controller {
	
	public function lerror($arg = null) {
		$this->view->view = 'error';
	}

}