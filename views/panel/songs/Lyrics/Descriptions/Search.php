<?php
namespace packages\ghafiye\views\panel\songs\lyrics\descriptions;
use \packages\base\views\traits\form as formTrait;
use \packages\ghafiye\{views\listview, authorization};

class Search extends listview {
	use formTrait;
	protected static $navigation;
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed("songs_lyrics_descriptions_search");
	}
	protected $canEdit;
	protected $canDel;
	function __construct(){
		$this->canEdit = authorization::is_accessed("songs_lyrics_descriptions_edit");
		$this->canDel = authorization::is_accessed("songs_lyrics_descriptions_delete");
	}
	public function getDescriptions(){
		return $this->getDataList();
	}
}
