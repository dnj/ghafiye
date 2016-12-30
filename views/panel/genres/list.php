<?php
namespace packages\ghafiye\views\panel\genre;
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
		$this->canAdd = authorization::is_accessed('genre_add');
		$this->canEdit = authorization::is_accessed('genre_edit');
		$this->canDel = authorization::is_accessed('genre_delete');
	}
	public function getgenresLists(){
		return $this->dataList;
	}

	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('genres_list');
	}
	public function export(){
		$export = parent::export();
		$export['data']['items'] = array();
		foreach($this->getgenresLists() as $genre){
			$item = $genre->toArray();
			$item['title'] = $genre->title('fa');
			$export['data']['items'][] = $item;
		}
		return $export;
	}
}
