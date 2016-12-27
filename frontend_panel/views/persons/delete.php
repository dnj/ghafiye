<?php
namespace themes\clipone\views\ghafiye\person;
use \packages\base\packages;
use \packages\base\translator;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\person\delete as personDelete;

class delete extends personDelete{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('persons'),
			translator::trans('delete')
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('persons'));
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('delete'));
		$item->setIcon('fa fa-trash-o');
		breadcrumb::addItem($item);
		navigation::active("persons");
	}
}
