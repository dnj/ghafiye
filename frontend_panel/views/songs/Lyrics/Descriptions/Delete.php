<?php
namespace themes\clipone\views\ghafiye\songs\lyrics\descriptions;
use packages\base\translator;
use themes\clipone\{viewTrait, navigation, views\formTrait};
use packages\ghafiye\{views\panel\songs\lyrics\descriptions\Delete as parentView, song\lyric\Description};

class Delete extends parentView {
	use viewTrait, formTrait;
	protected $description;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.lyrics.descriptions.delete"));
		$this->addBodyClass("songs-lyrics-delete");
		navigation::active("songs_lyrics");
		$this->description = $this->getLyricDescription();
	}
}
