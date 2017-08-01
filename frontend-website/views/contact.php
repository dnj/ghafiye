<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\translator;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
use \packages\ghafiye\views\contact as homepagecontact;
class contact extends homepagecontact{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('contact.title'));
		$this->addBodyClass('article');
		$this->addBodyClass('contact');
	}
}
