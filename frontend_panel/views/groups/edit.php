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
use \packages\ghafiye\views\panel\group\edit as groupEdit;

class edit extends groupEdit{
	use viewTrait, listTrait, formTrait;
	protected $group;
	function __beforeLoad(){
		$this->group = $this->getgroup();
		$this->setTitle(array(
			translator::trans('groups'),
			$this->group->first_name,
			translator::trans('edit')
		));
		$this->addBodyClass("group_edit");
		$this->addAssests();
		$this->setNavigation();
	}
	private function addAssests(){
		$this->addJSFile(theme::url("assets/js/pages/group.edit.js"));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/x-editable/js/bootstrap-editable.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/x-editable/css/bootstrap-editable.css'));
		$this->addJSFile(theme::url("assets/plugins/jquery.growl/javascripts/jquery.growl.js"));
		$this->addCSSFile(theme::url("assets/plugins/jquery.growl/stylesheets/jquery.growl.css"));
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('groups'));
		$item = new menuItem("edit");
		$item->setTitle(translator::trans('edit'));
		$item->setIcon('fa fa-edit');
		breadcrumb::addItem($item);
		navigation::active("groups");
	}
	protected function getImage($image){
		return packages::package('ghafiye')->url($image ? $image : options::get('packages.ghafiye.groups.deafault_image'));
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
