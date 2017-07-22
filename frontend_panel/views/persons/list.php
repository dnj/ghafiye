<?php
namespace themes\clipone\views\ghafiye\person;
use \packages\base\db;
use \packages\base\translator;
use \packages\base\packages;
use \packages\base\frontend\theme;

use \packages\userpanel;

use \packages\ghafiye\person;
use \packages\ghafiye\views\panel\person\listview as personsList;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

class listview extends personsList{
	use viewTrait,listTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('persons'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("persons");
	}
	public function setButtons(){
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-info')
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
			$persons = new menuItem("persons");
			$persons->setTitle(translator::trans("ghafiye.panle.persons.list"));
			$persons->setURL(userpanel\url('persons'));
			$persons->setIcon('fa fa-list-alt');
			navigation::addItem($persons);
		}
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
	protected function getAvatar(person $person):string{
		return $person->avatar ? packages::package('ghafiye')->url($person->avatar) : theme::url("assets/images/avatar-placeholder.png");
	}
	protected function songsCountByPerson(person $person):int{
		db::join("ghafiye_songs_persons", "ghafiye_songs_persons.song=ghafiye_songs.id", "inner");
		db::joinWhere("ghafiye_songs_persons", "ghafiye_songs_persons.person", $person->id);
		return db::getValue("ghafiye_songs", "count(*)");
	}
}
