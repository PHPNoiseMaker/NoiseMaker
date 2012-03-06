<?php
require_once(ROOT . DS . APP_DIR . '/controllers/AppController.php');
class ErrorsController extends AppController {
	
	public function index($arg = null) {
		$this->viewFile = 'error';
	}

}