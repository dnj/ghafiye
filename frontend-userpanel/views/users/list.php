<?php
namespace themes\clipone\views\users;
use \packages\userpanel\views\users\listview as usersListView;
use \packages\userpanel;
use \packages\userpanel\user;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;

class listview extends usersListView{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("users/list");
	}
	public function setButtons(){
		$this->setButton('view', $this->canView, array(
			'title' => translator::trans('user.profile'),
			'icon' => 'fa fa-credit-card',
			'classes' => array('btn', 'btn-xs', 'btn-green')
		));
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('user.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-teal')
		));
		$this->setButton('delete', $this->canDelete, array(
			'title' => translator::trans('user.delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$item = new menuItem("users");
			$item->setTitle('کاربران');
			$item->setURL(userpanel\url('users'));
			$item->setIcon('clip-users');
			navigation::addItem($item);
		}
	}
	public function getTypesForSelect(){
		$options = [array('title' => '', 'value' => '')];
		foreach($this->getUserTypes() as $type){
			$options[] = array(
				'title' => $type->title,
				'value' => $type->id
			);
		}
		return $options;
	}
	public function getStatusForSelect(){
		return array(
			array(
				'title' => '',
				'value' => ''
			),
			array(
				'title' => translator::trans('active'),
				'value' => user::active
			),
			array(
				'title' => translator::trans('suspend'),
				'value' => user::suspend
			),
			array(
				'title' => translator::trans('deactive'),
				'value' => user::deactive
			)
		);
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
