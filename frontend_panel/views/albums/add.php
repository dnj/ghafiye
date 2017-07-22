<?php
namespace themes\clipone\views\ghafiye\album;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\album;
use \packages\ghafiye\views\panel\album\add as albumADD;

class add extends albumADD{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('albums'),
			translator::trans('ghafiye.panle.albums.add')
		));
		$this->addBodyClass('album_add');
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('albums'));
		$item = new menuItem("add");
		$item->setTitle(translator::trans('add'));
		$item->setIcon('fa fa-plus');
		breadcrumb::addItem($item);
		navigation::active("albums");
	}
	protected function getImage(){
		return packages::package('ghafiye')->url("storage/public/default-image.png");
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
