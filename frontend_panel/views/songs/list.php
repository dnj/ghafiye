<?php
namespace themes\clipone\views\ghafiye\song;
use \packages\base;
use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\userpanel;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\ghafiye\views\panel\song\listview as songsList;

class listview extends songsList{
	use viewTrait,listTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('ghafiye.panel.songs'));
		$this->setButtons();
		$this->addBodyClass('song_list');
		navigation::active("songs");
	}
	public function setButtons(){
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-teal')
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
			$songs = new menuItem("songs");
			$songs->setTitle(translator::trans("ghafiye.panel.songs"));
			$songs->setURL(userpanel\url('songs'));
			$songs->setIcon('fa fa-music');
			navigation::addItem($songs);
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
