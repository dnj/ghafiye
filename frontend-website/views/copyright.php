<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\copyright as copyrightView;
class copyright extends copyrightView{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('copyright.title'));
		$this->addBodyClass('copyright');
		$this->addBodyClass('article');
	}
}
