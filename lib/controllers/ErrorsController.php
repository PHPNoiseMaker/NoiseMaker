<?php
require_once('Controllers/AppController.php');
class ErrorsController extends AppController {
	
	public function index($arg = null) {
		$this->viewFile = 'error';
	}

}