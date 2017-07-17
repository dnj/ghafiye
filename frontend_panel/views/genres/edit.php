<?php
namespace themes\clipone\views\ghafiye\genre;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\genre\edit as genreEdit;

class edit extends genreEdit{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('genres'),
			translator::trans('edit')
		));
		$this->addBodyClass('genres');
		$this->addBodyClass('genre_edit');
		$this->setNavigation();
	}

	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('genres'));
		$item = new menuItem("edit");
		$item->setTitle(translator::trans('edit'));
		$item->setIcon('fa fa-edit');
		breadcrumb::addItem($item);
		navigation::active("genres");
	}
	protected function getLangsForSelect(){
		$langs = array();
		foreach(translator::$allowlangs as $lang){
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		return $langs;
	}
}
