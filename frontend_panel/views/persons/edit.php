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
use \packages\ghafiye\views\panel\person\edit as personEdit;

class edit extends personEdit{
	use viewTrait, listTrait, formTrait;
	protected $person;
	function __beforeLoad(){
		$this->person = $this->getperson();
		$this->setTitle(array(
			translator::trans('persons'),
			$this->person->first_name,
			translator::trans('edit')
		));
		$this->addBodyClass("person_edit");
		$this->setButtons();
		$this->setNavigation();
	}
	public function setButtons(){
		$this->setButton('delete', $this->canNameDel, array(
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky', 'lang-del')
		));
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('persons'));
		$item = new menuItem("edit");
		$item->setTitle(translator::trans('edit'));
		$item->setIcon('fa fa-edit');
		breadcrumb::addItem($item);
		navigation::active("persons");
	}
	protected function getImage($image){
		return $image ? packages::package('ghafiye')->url($image) : theme::url("assets/images/avatar-placeholder.png");
	}
	protected function defaultAvatar():string{
		return theme::url("assets/images/avatar-placeholder.png");
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
	protected function getGenderForSelect(){
		return array(
			array(
				'title' => translator::trans("ghafiye.panel.person.gender.unknown"),
				'value' => ''
			),
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
}
