<?php
namespace packages\ghafiye\controllers;
use packages\base;
use packages\base\NotFound;
use packages\userpanel\controllers\login as userpanelLogin;
use packages\ghafiye\{view, views, controller, authentication, authorization};

class Login extends controller {
	public function register() {
		if (authentication::check()) {
			$this->response->Go(base\url());
			return $this->response;
		}
		$inputsRules = array(
			"name" => array(
				"type" => "string",
			),
			"lastname" => array(
				"type" => "string",
				"optional" => true,
				"empty" => true,
			),
			"email" => array(
				"type" => "email",
			),
			"password" => array(
				"type" => "string",
			),
			"tos" => array(
				"values" => array(1)
			),
		);
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["lastname"])) {
			if (!$inputs["lastname"]) {
				unset($inputs["lastname"]);
			}
		}
		$login = new userpanelLogin();
		$user = $login->register_helper($inputsRules);
		$this->response->setStatus(true);
		$this->response->Go(base\url());
		return $this->response;
	}
	public function validate($data) {
		if (authentication::check() and !authorization::is_accessed("can_login_in_userpanel")) {
			throw new NotFound();
		}
		return true;
	}
}
