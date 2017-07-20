<?php
namespace packages\ghafiye\views\panel\crawler;
use \packages\ghafiye\views\listview;
use \packages\ghafiye\authorization;
use \packages\base\views\traits\form as formTrait;
class search extends listview{
	use formTrait;
	protected $canAdd;
	protected $canEdit;
	protected $canDel;
	static protected $navigation;
	function __construct(){
		$this->canAdd = authorization::is_accessed('crawler_add');
		$this->canEdit = authorization::is_accessed('crawler_edit');
		$this->canDel = authorization::is_accessed('crawler_delete');
	}
	public function getCrawlerLists(){
		return $this->dataList;
	}
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('crawler_search');
	}
}
