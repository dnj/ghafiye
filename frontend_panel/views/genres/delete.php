<?php
namespace themes\clipone\views\ghafiye\genre;
use \packages\base\translator;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;
use \packages\ghafiye\views\panel\genre\delete as genreDelete;
class delete extends genreDelete{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('genres'),
			translator::trans('ghafiye.panle.genres.delete')
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('genres'));
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('delete'));
		$item->setIcon('fa fa-traash-o');
		breadcrumb::addItem($item);
		navigation::active("genres");
	}
}
