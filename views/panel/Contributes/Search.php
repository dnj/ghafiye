<?php
namespace packages\ghafiye\views\panel\contributes;
use packages\base\views\traits\form as formTrait;
use packages\ghafiye\{views\listview, authorization};

class Search extends listview {
	use formTrait;
	public static function onSourceLoad() {
		self::$navigation = authorization::is_accessed("contributes_search");
	}
	protected static $navigation;
	protected $canDel;
	protected $canEdit;
	protected $canView;
	public function __construct() {
		$this->canView = authorization::is_accessed("contributes_view");
		$this->canEdit = authorization::is_accessed("contributes_edit");
		$this->canDel = authorization::is_accessed("contributes_delete");
	}
	public function getContributes() {
		return $this->getDataList();
	}
}
