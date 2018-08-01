<?php
namespace themes\clipone\views\ghafiye\songs\lyrics\descriptions;
use packages\base\translator;
use themes\clipone\{viewTrait, navigation, views\formTrait};
use packages\ghafiye\{views\panel\songs\lyrics\descriptions\Edit as parentView, song\lyric\Description};

class Edit extends parentView {
	use viewTrait, formTrait;
	protected $description;
	public function __beforeLoad(){
		$this->setTitle(translator::trans("ghafiye.panel.songs.lyrics.descriptions.edit"));
		$this->addBodyClass("songs-lyrics-edit");
		navigation::active("songs_lyrics");
		$this->description = $this->getLyricDescription();
	}
	public function getStatusForSelect() {
		return array(
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
}
