<?php
namespace themes\clipone\views\ghafiye\songs\comments;
use packages\base\translator;
use packages\userpanel;
use themes\clipone\{viewTrait, navigation};
use packages\ghafiye\{views\panel\songs\comments\Delete as parentView, song\Comment};

class Delete extends parentView {
	use viewTrait;
	protected $comment;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.comments.delete"));
		$this->addBodyClass("songs-comments");
		navigation::active("songs_comments");
		$this->comment = $this->getComment();
	}
}
