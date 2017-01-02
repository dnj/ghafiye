<?php
namespace themes\musixmatch\views\search;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\ghafiye\views\search\index as homepage;

use \themes\musixmatch\viewTrait;
use \themes\musixmatch\views\formTrait;
class index extends homepage{
	use viewTrait;
	function __beforeLoad(){
		$this->addBodyClass('search');
	}
}
