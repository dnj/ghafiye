<?php
namespace themes\clipone\views\ghafiye\songs\comments;
use packages\base\translator;
use packages\userpanel;
use themes\clipone\{viewTrait, navigation};
use packages\ghafiye\{views\panel\songs\comments\View as parentView, song\Comment};

class View extends parentView {
	use viewTrait;
	protected $comment;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.comments.view"));
		$this->addBodyClass("songs-comments");
		navigation::active("songs_comments");
		$this->comment = $this->getComment();
	}
}
