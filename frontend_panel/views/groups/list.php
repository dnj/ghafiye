<?php
namespace themes\clipone\views\ghafiye\group;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\userpanel;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\group\listview as groupsList;

class listview extends groupsList{
	use viewTrait,listTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('groups'),
			translator::trans('list')
		));
		$this->setButtons();
		$this->addAssets();
		navigation::active("groups");
	}
	private function addAssets(){
		$this->addJSFile(theme::url("assets/js/pages/group.list.js"));
	}
	public function setButtons(){
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning')
		));
		$this->setButton('delete', $this->canDel, array(
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$groups = new menuItem("groups");
			$groups->setTitle(translator::trans("ghafiye.panle.groups.list"));
			$groups->setURL(userpanel\url('groups'));
			$groups->setIcon('fa fa-list-alt');
			navigation::addItem($groups);
		}
	}
	protected function getLnagsForSelect(){
		$langs = array(
			array(
				'title' => translator::trans("choose"),
				'value' => ''
			)
		);
		foreach(translator::$allowlangs as $lang){
			$langs[] = array(
				'title' => translator::trans("translations.langs.{$lang}"),
				'value' => $lang
			);
		}
		return $langs;
	}
	public function getComparisonsForSelect(){
		return array(
			array(
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			),
			array(
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			),
			array(
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			)
		);
	}
}
