<?php
namespace themes\musixmatch\views;
use \packages\base;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \packages\ghafiye\views\notfound as notfoundView;
class notfound extends notfoundView{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('notfound.title'));
		$this->addBodyClass('error');
		$this->addBodyClass('notfound');
	}
}
