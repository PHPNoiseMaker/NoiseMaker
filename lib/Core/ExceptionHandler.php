<?php

class ExceptionHandler {
	public static function handleException(Exception $e) {
		require_once 'lib/Controllers/ErrorsController.php';
		$controller = new ErrorsController(new Request(), new Response());
		
		$params = array(
			'error' => array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			)
		);
		$controller->index($params);
		$controller->render('Errors');
		exit;
	}
	
	public static function handleError($errorNumber, $errorMsg, $errfile, $errline) {
		require_once 'lib/Controllers/ErrorsController.php';
		$controller = new ErrorsController(new Request(), new Response());
		
		$params = array(
			'error' => array(
				'message' => $errorMsg,
				'code' => $errorNumber,
				'file' => $errfile,
				'line' => $errline
			)
		);
		$controller->index($params);
		$controller->render('Errors');
		exit;
	}
}