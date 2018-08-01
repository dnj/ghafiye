<?php
namespace packages\ghafiye\views\panel\songs\comments;
use \packages\ghafiye\{view, song\Comment};

class Delete extends view {
	public function setComment(Comment $comment){
		$this->setData($comment, "comment");
	}
	protected function getComment() {
		return $this->getData("comment");
	}
}
