<?php
require_once(ROOT . DS . APP_DIR . '/controllers/AppController.php');
class PagesController extends AppController {
	
	public function index($page = null) {
		if($page === null) {
			$page = 'index';
		}
		switch($page) {
			case 'index':
				$this->viewFile = 'index';
				break;
			default:
				throw new Exception('test');
		}
	}

}