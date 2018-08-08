<?php
namespace packages\ghafiye\views\contributes\songs;
use packages\ghafiye\{views\form, song};

class Edit extends form {
	protected $lyrics = array();
	public function setSong(song $song) {
		$this->setData($song, "song");
		$this->setDataForm($song->title($song->lang), "title");
		$lyrics = array();
		$i = 0;
		foreach ($this->getLyrics() as $lyric) {
			$lyrics[$i++] = array(
				"id" => $lyric->id,
				"text" => $lyric->text,
			);
		}
		$this->setDataForm($lyrics, "lyrics");
	}
	protected function getSong() {
		return $this->getData("song");
	}
	protected function getLyrics(): array {
		if (!$this->lyrics) {
			$song = $this->getSong();
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("lang", $song->lang);
			$lyric->where("status", song\lyric::published);
			$lyric->orderBy("ordering", "ASC");
			$this->lyrics = $lyric->get();
		}
		return $this->lyrics;
	}
}
