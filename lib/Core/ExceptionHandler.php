<?php

class ExceptionHandler {
	public static function handleException(Exception $e) {
		require_once 'lib/Controllers/ErrorsController.php';
		$request = new Request();
		$controller = new ErrorsController(&$request, new Response());
		
		$requestedURI = '/' . implode('/', $request->getURI());
		
		$params = array(
			'error' => array(
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
				'uri' =>  $requestedURI
			)
		);
		$controller->index($params);
		$controller->render('Errors');
	}
}