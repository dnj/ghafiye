<?php
namespace themes\clipone\views\ghafiye\group;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\group;
use \packages\ghafiye\views\panel\group\add as groupADD;

class add extends groupADD{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('groups'),
			translator::trans('add')
		));
		$this->addBodyClass("group_add");
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('groups'));
		$item = new menuItem("add");
		$item->setTitle(translator::trans('add'));
		$item->setIcon('fa fa-plus');
		breadcrumb::addItem($item);
		navigation::active("groups");
	}
	protected function getImage(){
		return packages::package('ghafiye')->url(options::get('packages.ghafiye.groups.deafault_image'));
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
