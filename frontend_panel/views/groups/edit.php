<?php
namespace themes\clipone\views\ghafiye\group;
use \packages\base\options;
use \packages\base\packages;
use \packages\base\translator;
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
			translator::trans('edit')
		));
		$this->addBodyClass("group_edit");
		$this->setNavigation();
	}
	private function setNavigation(){
		breadcrumb::addItem(navigation::getByName('groups'));
		$item = new menuItem("edit");
		$item->setTitle(translator::trans('edit'));
		$item->setIcon('fa fa-edit');
		breadcrumb::addItem($item);
		navigation::active("groups");
	}
	protected function getImage(){
		return packages::package('ghafiye')->url($this->group->avatar ? $this->group->avatar : options::get('packages.ghafiye.groups.deafault_image'));
	}
	protected function defaultCover():string{
		return packages::package('ghafiye')->url($this->group->cover ? $this->group->cover : "storage/public/cover_placeholder.png");
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
