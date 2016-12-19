<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\about as aboutus;
class about extends aboutus{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('about.title'));
		$this->addBodyClass('about');
		$this->addBodyClass('article');
	}
}
