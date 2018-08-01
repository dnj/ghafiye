<?php
namespace packages\ghafiye\views\panel\songs\comments;
use \packages\ghafiye\{view as viewTrait, authorization, song\Comment};

class View extends viewTrait {
	protected $canEdit;
	public function __construct(){
		$this->canEdit = authorization::is_accessed("songs_comments_edit");
	}
	public function setComment(Comment $comment){
		$this->setData($comment, "comment");
	}
	protected function getComment() {
		return $this->getData("comment");
	}
}
