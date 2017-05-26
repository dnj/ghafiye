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
		$this->addAssests();
		$this->setButtons();
		$this->setNavigation();
	}
	private function addAssests(){
		$this->addJSFile(theme::url("assets/js/pages/person.edit.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/x-editable/js/bootstrap-editable.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/x-editable/css/bootstrap-editable.css'));
		$this->addJSFile(theme::url("assets/plugins/jquery.growl/javascripts/jquery.growl.js"));
		$this->addCSSFile(theme::url("assets/plugins/jquery.growl/stylesheets/jquery.growl.css"));
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
		return packages::package('ghafiye')->url($image ? $image : options::get('packages.ghafiye.persons.deafault_image'));
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
