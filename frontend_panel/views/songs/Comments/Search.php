<?php
namespace themes\clipone\views\ghafiye\songs\comments;
use packages\base\{translator, view\error};
use packages\userpanel;
use themes\clipone\{viewTrait, navigation, views\listTrait, views\formTrait, navigation\menuItem};
use packages\ghafiye\{views\panel\songs\comments\Search as parentView, song\Comment};

class Search extends parentView {
	use viewTrait, listTrait, formTrait;
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if (parent::$navigation) {
			$comment = new menuItem("songs_comments");
			$comment->setTitle(translator::trans("ghafiye.panel.songs.comments"));
			$comment->setURL(userpanel\url("songs/comments"));
			$comment->setIcon("fa fa-comments");
			navigation::addItem($comment);
		}
	}
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.comments"));
		$this->setButtons();
		$this->addBodyClass("songs-comments");
		navigation::active("songs_comments");
		$this->comments = $this->getComments();
		if (!$this->comments) {
			$this->addEmptyCommentsError();
		}
	}
	public function setButtons(){
		$this->setButton("view", $this->canView, array(
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
				"title" => translator::trans("ghafiye.panel.song.comments.status.accept"),
				"value" => Comment::accepted,
			),
			array(
				"title" => translator::trans("ghafiye.panel.song.comments.status.waitForAccept"),
				"value" => Comment::waitForAccept,
			),
			array(
				"title" => translator::trans("ghafiye.panel.song.comments.status.rejected"),
				"value" => Comment::rejected,
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
	protected function addEmptyCommentsError() {
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode("ghafiye.panel.songs.comments.empty");
		$this->addError($error);
	}
}
