<?php
require_once('Controllers/AppController.php');
class PagesController extends AppController {
	
	public function index($page = null) {
		$numArgs = func_num_args();
		$args = func_get_args();
	 	
		if($page === null) {
			$page = 'index';
		} else {
			$page = $page;
		}
		if($page === 'index') {
			$this->viewFile = 'index';
		} else {
			
			if($this->View->viewExists('Pages', $page)) {
				$this->viewFile = $page;
			} else {
				throw new NotFoundException();
			}
			
					
		}
	}

}