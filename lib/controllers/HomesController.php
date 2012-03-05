<?php
require_once(ROOT . DS . APP_DIR . '/lib/controller.php');
class HomesController extends Controller {
	
	public function index() {
		$this->view->view = 'index';
	}

}