<?php
App::uses('AppController', 'Controller');
/**
 * PagesController class.
 * 
 * @extends AppController
 */
class PagesController extends AppController {
	
	public $uses = false;
	/**
	 * index function.
	 * 
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->display();
	}
	
	/**
	 * display function.
	 * 
	 * @access public
	 * @return void
	 */
	public function display() {
		$args = func_get_args();

		if (
			isset($args[0])
			&& !empty($args[0])
		) {
			$page = $args[0];
		} else {
			$page = 'index';
		}
		if (View::viewExists('Pages', $page)) {
			$this->view = $page;
			
		} else {
			
			throw new NotFoundException();
		}
			
	}

}