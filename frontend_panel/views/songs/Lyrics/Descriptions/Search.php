<?php
namespace themes\clipone\views\ghafiye\songs\lyrics\descriptions;
use packages\base\{translator, view\error};
use packages\userpanel;
use themes\clipone\{viewTrait, navigation, views\listTrait, views\formTrait, navigation\menuItem};
use packages\ghafiye\{views\panel\songs\lyrics\descriptions\Search as parentView, song\lyric\Description, authorization};

class Search extends parentView {
	use viewTrait, listTrait, formTrait;
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if (parent::$navigation) {
			$comment = new menuItem("songs_lyrics");
			$comment->setTitle(translator::trans("ghafiye.panel.songs.lyrics.descriptions"));
			$comment->setURL(userpanel\url("songs/lyrics/descriptions"));
			$comment->setIcon("fa fa-question");
			navigation::addItem($comment);
		}
	}
	protected $hasChildrenType;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.lyrics.descriptions"));
		$this->setButtons();
		$this->addBodyClass("songs-lyrics");
		navigation::active("songs_lyrics");
		$this->descriptions = $this->getDescriptions();
		if (!$this->descriptions) {
			$this->addEmptyDescriptionsError();
		}
		$this->hasChildrenType = (bool)authorization::childrenTypes();
	}
	public function setButtons(){
		$this->setButton("edit", $this->canEdit, array(
			"title" => translator::trans("edit"),
			"icon" => "fa fa-edit",
			"classes" => array("btn", "btn-xs", "btn-teal")
		));
		$this->setButton("delete", $this->canDel, array(
			"title" => translator::trans("delete"),
			"icon" => "fa fa-times",
			"classes" => array("btn", "btn-xs", "btn-bricky")
		));
	}
	public function getStatusForSelect() {
		return array(
			array(
				"title" => translator::trans("ghafiye.choose"),
				"value" => "",
			),
			array(
				"title" => translator::trans("ghafiye.panel.song.lyrics.descriptions.status.accepted"),
				"value" => Description::accepted,
			),
			array(
				"title" => translator::trans("ghafiye.panel.song.lyrics.descriptions.status.waitForAccept"),
				"value" => Description::waitForAccept,
			),
			array(
				"title" => translator::trans("ghafiye.panel.song.lyrics.descriptions.status.rejected"),
				"value" => Description::rejected,
			),
		);
	}
	public function getComparisonsForSelect(){
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
	protected function addEmptyDescriptionsError() {
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode("ghafiye.panel.songs.lyrics.desctipions.empty");
		$this->addError($error);
	}
}
