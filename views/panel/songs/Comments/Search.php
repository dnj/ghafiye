<?php
namespace packages\ghafiye\views\panel\songs\comments;
use \packages\base\views\traits\form as formTrait;
use \packages\ghafiye\{views\listview, authorization};

class Search extends listview {
	use formTrait;
	protected static $navigation;
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed("songs_comments_search");
	}
	protected $canView;
	protected $canDel;
	function __construct(){
		$this->canView = authorization::is_accessed("songs_comments_view");
		$this->canDel = authorization::is_accessed("songs_comments_delete");
	}
	public function getComments(){
		return $this->dataList;
	}
}
