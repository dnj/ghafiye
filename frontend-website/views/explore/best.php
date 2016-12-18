<?php
namespace themes\musixmatch\views\explore;
use \packages\base;
use \packages\base\translator;
use \packages\ghafiye\views\explore\best as bestView;
use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\musicTrait;
class best extends bestView{
	use viewTrait,musicTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('explore.best.title'));
		$this->addBodyClass('explore');
	}
}
