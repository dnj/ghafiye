<?php
namespace themes\clipone\views\ghafiye\genre;
use \packages\base\translator;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;
use \packages\ghafiye\views\panel\genre\add as genreADD;
class add extends genreADD{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('genres'),
			translator::trans('ghafiye.panle.genres.add')
		));
		$this->addBodyClass('genres');
		$this->addBodyClass('genre_add');
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('genres'));
		$item = new menuItem("add");
		$item->setTitle(translator::trans('add'));
		$item->setIcon('fa fa-plus');
		breadcrumb::addItem($item);
		navigation::active("genres");
	}
	protected function getLangsForSelect(){
		$langs = [];
		foreach(['en', 'fa'] as $lang){
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		foreach(translator::$allowlangs as $lang){
			if(in_array($lang, ['en', 'fa'])){
				continue;
			}
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		return $langs;
	}
}
