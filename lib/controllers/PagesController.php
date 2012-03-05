<?php
require_once(ROOT . DS . APP_DIR . '/lib/controller.php');
class PagesController extends Controller {
	
	public function index($page = null) {
		if($page === null) {
			$page = 'index';
		}
		switch($page) {
			case 'index':
				$this->view->view = 'index';
				break;
			default:
				throw new Exception('test');
		}
	}

}