<?php
namespace themes\musixmatch\views\explore;
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\views\explore\lastest as lastestView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\listTrait;
use \themes\musixmatch\views\musicTrait;

class lastest extends lastestView{
	use viewTrait,listTrait,musicTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('explore.lastest.title'));
		$this->addBodyClass('explore');
	}
}
