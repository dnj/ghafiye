<?php
namespace packages\ghafiye\views\panel\group;
use \packages\ghafiye\views\listview as list_view;
use \packages\ghafiye\authorization;
use \packages\base\views\traits\form as formTrait;
class listview extends list_view{
	use formTrait;
	protected $canAdd;
	protected $canEdit;
	protected $canDel;
	static protected $navigation;
	function __construct(){
		$this->canAdd = authorization::is_accessed('group_add');
		$this->canEdit = authorization::is_accessed('group_edit');
		$this->canDel = authorization::is_accessed('group_delete');
	}
	public function getGroupsLists(){
		return $this->dataList;
	}

	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('groups_list');
	}
}
