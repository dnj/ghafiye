<?php
namespace packages\ghafiye\views\panel\album;
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
		$this->canAdd = authorization::is_accessed('album_add');
		$this->canEdit = authorization::is_accessed('album_edit');
		$this->canDel = authorization::is_accessed('album_delete');
	}
	public function getAlbumsLists(){
		return $this->dataList;
	}

	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('albums_list');
	}
	public function export(){
		$export = parent::export();
		$export['data']['items'] = array();
		foreach($this->getAlbumsLists() as $album){
			$item = $album->toArray();
			$item['title'] = $album->title($album->lang);
			$export['data']['items'][] = $item;
		}
		return $export;
	}
}
