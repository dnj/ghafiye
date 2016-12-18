<?php
namespace packages\ghafiye\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\inputValidation;
use \packages\base\views\FormError;

use \packages\ghafiye\controller;
use \packages\ghafiye\view;
use \packages\ghafiye\contact;

use \packages\chatdesign\plan;
class home extends controller{
	public function index(){
		$view = view::byName("\\packages\\ghafiye\\views\\index");
		$this->response->setView($view);
		return $this->response;
	}
	public function contact(){
		$view = view::byName("\\packages\\ghafiye\\views\\contact");
		$this->response->setStatus(false);
		if(http::is_post()){
			$inputsRules = array(
				'name' => array(
					'type' => 'string'
				),
				'email' => array(
					'type' => 'email',
				),
				'subject' => array(
					'type' => 'string'
				),
				'text' => array(
					'type' => 'string'
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$contact = new contact($inputs);
				$contact->ip = http::$client['ip'];
				$contact->save();
				$this->response->setStatus(true);
			}catch(inputValidation $error){
				echo $error;
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function about(){
		$view = view::byName("\\packages\\ghafiye\\views\\about");
		$this->response->setView($view);
		return $this->response;
	}
}
