<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base\translator;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\song\delete as songDelete;

class delete extends songDelete{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('songs'),
			translator::trans('delete')
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('songs'));
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('delete'));
		$item->setIcon('fa fa-trash-o');
		breadcrumb::addItem($item);
		navigation::active("songs");
	}
}
