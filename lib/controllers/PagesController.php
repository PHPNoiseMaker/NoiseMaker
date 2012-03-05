<?php
require_once(ROOT . DS . APP_DIR . '/lib/controller.php');
class PagesController extends Controller {
	
	public function index() {
		$this->view->view = 'index';
	}

}