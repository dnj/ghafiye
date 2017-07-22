<?php
namespace themes\musixmatch\views\blog;
use \packages\base\translator;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
use \packages\blog\views\post\notfound as notfoundView;
class notfound extends notfoundView{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('notfound.title'));
		$this->addBodyClass('error');
		$this->addBodyClass('notfound');
	}
}
