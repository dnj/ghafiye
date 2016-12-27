<?php
namespace themes\clipone\views\ghafiye\album;
use \packages\base\translator;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\album\delete as albumDelete;

class delete extends albumDelete{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('albums'),
			translator::trans('delete')
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('albums'));
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('delete'));
		$item->setIcon('fa fa-trash-o');
		breadcrumb::addItem($item);
		navigation::active("albums");
	}
}
