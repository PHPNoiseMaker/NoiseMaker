<?php

class ExceptionHandler {
	public static function handleException(Exception $e) {
		require_once 'lib/Controllers/ErrorsController.php';
		$controller = new ErrorsController($request = new Request(), new Response());
		
		$params = array(
			'error' => array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			)
		);
		$controller->index($params);
		$controller->render('Errors');
	}
}