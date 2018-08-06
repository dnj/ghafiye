<?php
namespace packages\ghafiye\contributes;
use packages\base\view\error;

class Exception extends \Exception {}

class preventRejectException extends Exception {
	protected $errors = array();
	public function __construct(error $error, string $message = "") {
		$this->errors[] = $error;
	}
	public function addError(error $error) {
		$this->errors[] = $error;
	}
	public function getErrors(): array {
		return $this->errors;
	}
}
class preventDeleteException extends Exception {
	protected $errors = array();
	public function __construct(error $error, string $message = "") {
		$this->errors[] = $error;
	}
	public function addError(error $error) {
		$this->errors[] = $error;
	}
	public function getErrors(): array {
		return $this->errors;
	}
}
