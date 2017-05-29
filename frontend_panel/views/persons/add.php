<?php
namespace themes\clipone\views\ghafiye\person;
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

use \packages\ghafiye\person;
use \packages\ghafiye\views\panel\person\add as personADD;

class add extends personADD{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('persons'),
			translator::trans('add')
		));
		$this->addBodyClass("person_add");
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('persons'));
		$item = new menuItem("add");
		$item->setTitle(translator::trans('add'));
		$item->setIcon('fa fa-plus');
		breadcrumb::addItem($item);
		navigation::active("persons");
	}
	protected function getImage(){
		return packages::package('ghafiye')->url(options::get("packages.ghafiye.persons.deafault_image"));
	}
	protected function getGenderForSelect(){
		return array(
			array(
				'title' => translator::trans("ghafiye.panel.person.gender.men"),
				'value' => person::men
			),
			array(
				'title' => translator::trans("ghafiye.panel.person.gender.women"),
				'value' => person::women
			)
		);
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
