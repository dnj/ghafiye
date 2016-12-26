<?php
namespace themes\clipone\views\ghafiye\group;
use \packages\base\translator;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\group\delete as groupDelete;

class delete extends groupDelete{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('groups'),
			translator::trans('delete')
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('groups'));
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('delete'));
		$item->setIcon('fa fa-trash-o');
		breadcrumb::addItem($item);
		navigation::active("groups");
	}
}
