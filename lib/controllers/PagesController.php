<?php
require_once('Controllers/AppController.php');
class PagesController extends AppController {
	
	public function index() {
		$this->display();
	}
	
	public function display() {
		$args = func_get_args();
		$count = count($args);

		if(
			isset($args[0])
			&& !empty($args[0])
		) {
			$page = $args[0];
		} else {
			$page = 'index';
		}
		if($this->View->viewExists('Pages', $page)) {
			$this->view = $page;
			
		} else {
			
			throw new NotFoundException();
		}
			
	}

}