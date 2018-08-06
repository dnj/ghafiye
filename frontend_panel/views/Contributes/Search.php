<?php
namespace themes\clipone\views\ghafiye\contributes;
use packages\userpanel;
use packages\base\{translator, view\error};
use packages\ghafiye\{views\panel\contributes\Search as parentView, Contribute, authorization};
use themes\clipone\{viewTrait, navigation, views\listTrait, views\formTrait, navigation\menuItem};

class Search extends parentView {
	use viewTrait, listTrait, formTrait;
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$contributes = new menuItem("contributes");
			$contributes->setTitle(translator::trans("ghafiye.panel.contributes"));
			$contributes->setURL(userpanel\url("contributes"));
			$contributes->setIcon("fa fa-wrench");
			navigation::addItem($contributes);
		}
	}
	protected $contributes;
	protected $childrenTypes;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.contributes"));
		$this->setButtons();
		$this->contributes = $this->getContributes();
		$this->addBodyClass("contributes");
		$this->addBodyClass("contributes-search");
		navigation::active("contributes");
		if (!$this->contributes) {
			$this->addEmptyContrubuteError();
		}
		$this->childrenTypes = (int)authorization::childrenTypes();
	}
	public function setButtons(){
		$this->setButton("view", $this->canView, array(
			"title" => translator::trans("ghafiye.view"),
			"icon" => "fa fa-file-o",
			"classes" => array("btn", "btn-xs", "btn-info"),
		));
		$this->setButton("accept", $this->canEdit, array(
			"title" => translator::trans("ghafiye.accept"),
			"icon" => "fa fa-check-square-o",
			"classes" => array("btn", "btn-xs", "btn-success"),
		));
		$this->setButton("reject", $this->canEdit, array(
			"title" => translator::trans("ghafiye.reject"),
			"icon" => "fa fa-ban",
			"classes" => array("btn", "btn-xs", "btn-warning"),
		));
		$this->setButton("delete", $this->canDel, array(
			"title" => translator::trans("ghafiye.delete"),
			"icon" => "fa fa-times",
			"classes" => array("btn", "btn-xs", "btn-bricky"),
		));
	}
	public function getComparisonsForSelect() {
		return array(
			array(
				"title" => translator::trans("search.comparison.contains"),
				"value" => "contains"
			),
			array(
				"title" => translator::trans("search.comparison.equals"),
				"value" => "equals"
			),
			array(
				"title" => translator::trans("search.comparison.startswith"),
				"value" => "startswith"
			),
		);
	}
	protected function addEmptyContrubuteError() {
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode("ghafiye.panel.contributes.empty");
		$this->addError($error);
	}
	protected function getStatusForSelect(): array {
		return array(
			array(
				"title" => translator::trans("ghafiye.choose"),
				"value" => "",
			),
			array(
				"title" => translator::trans("ghafiye.panel.contributes.status.accepted"),
				"value" => Contribute::accepted,
			),
			array(
				"title" => translator::trans("ghafiye.panel.contributes.status.waitForAccept"),
				"value" => Contribute::waitForAccept,
			),
			array(
				"title" => translator::trans("ghafiye.panel.contributes.status.rejected"),
				"value" => Contribute::rejected,
			),
		);
	}
}
