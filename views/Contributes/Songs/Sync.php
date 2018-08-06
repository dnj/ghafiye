<?php
namespace packages\ghafiye\views\contributes\songs;
use packages\ghafiye\{views\form, song};

class Sync extends form {
	protected $lyrics = array();
	public function setSong(song $song) {
		$this->setData($song, "song");
		$time = array();
		foreach ($this->getLyrics() as $lyric) {
			$time[$lyric->id] = $this->formatTime($lyric->time);
		}
		$this->setDataForm($time, "time");
	}
	public function getLyrics(): array {
		if (!$this->lyrics) {
			$song = $this->getSong();
			$lyric = new song\lyric();
			$lyric->where("song", $song->id);
			$lyric->where("lang", $song->lang);
			$this->lyrics = $lyric->get();
		}
		return $this->lyrics;
	}
	protected function getSong() {
		return $this->getData("song");
	}
	protected function formatTime($time): string {
		$min = floor($time / 60);
		$sec = $time % 60;
		return ($min < 10 ? "0" : "") . $min . ":" . ($sec < 10 ? "0" : "") . $sec;
	}
}
