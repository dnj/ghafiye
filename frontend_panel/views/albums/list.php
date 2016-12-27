<?php
namespace themes\clipone\views\ghafiye\album;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\userpanel;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\album\listview as albumsList;

class listview extends albumsList{
	use viewTrait,listTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('albums'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("albums");
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
			$albums = new menuItem("albums");
			$albums->setTitle(translator::trans("ghafiye.panle.albums.list"));
			$albums->setURL(userpanel\url('albums'));
			$albums->setIcon('fa fa-file-audio-o');
			navigation::addItem($albums);
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
