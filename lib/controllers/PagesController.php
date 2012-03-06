<?php
require_once('Controllers/AppController.php');
class PagesController extends AppController {
	
	public function index($page = null) {
		if($page === null) {
			$page = 'index';
		}
		if($page === 'index') {
			$this->viewFile = 'index';
		} else {
			
			if($this->view->viewExists('Pages', $page)) {
				$this->viewFile = $page;
			} else {
				throw new Exception('Not Found');
			}
			
					
		}
	}

}