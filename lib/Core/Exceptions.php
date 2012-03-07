<?php
/**
 * Exception Definitions
 */
if (!class_exists('HttpException')) {
	class HttpException extends RuntimeException {
	}
}

class BadRequestException extends HttpException {

	public function __construct($message = null, $code = 400) {
		if (empty($message)) {
			$message = 'Bad Request';
		}
		parent::__construct($message, $code);
	}

}


class UnauthorizedException extends HttpException {

	public function __construct($message = null, $code = 401) {
		if (empty($message)) {
			$message = 'Unauthorized';
		}
		parent::__construct($message, $code);
	}

}

class ForbiddenException extends HttpException {

	public function __construct($message = null, $code = 403) {
		if (empty($message)) {
			$message = 'Forbidden';
		}
		parent::__construct($message, $code);
	}

}


class NotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Not Found';
		}
		parent::__construct($message, $code);
	}

}

class ViewNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'View File Not Found';
		}
		parent::__construct($message, $code);
	}

}
class LayoutNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'View File Not Found';
		}
		parent::__construct($message, $code);
	}

}
class ControllerNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Controller Not Found';
		}
		parent::__construct($message, $code);
	}

}

class ModelNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Model Not Found';
		}
		parent::__construct($message, $code);
	}

}


class MethodNotAllowedException extends HttpException {

	public function __construct($message = null, $code = 405) {
		if (empty($message)) {
			$message = 'Method Not Allowed';
		}
		parent::__construct($message, $code);
	}

}


class InternalErrorException extends HttpException {


	public function __construct($message = null, $code = 500) {
		if (empty($message)) {
			$message = 'Internal Server Error';
		}
		parent::__construct($message, $code);
	}

}
