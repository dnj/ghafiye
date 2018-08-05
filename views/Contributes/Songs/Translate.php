<?php
namespace packages\ghafiye\views\contributes\songs;
use packages\ghafiye\{views\form, song};

class Translate extends form {
	public function export(): array {
		$export = $items = array();
		if ($lyrics = $this->getTranslateLyrics()) {
			foreach ($lyrics as $lyric) {
				$items[] = array(
					"parent" => $lyric->parent,
					"text" => $lyric->text,
				);
			}
		}
		$export["data"]["items"] = $items;
		$errors = array();
		foreach ($this->getErrors() as $error) {
			$errors[] = array(
				"type" => $error->getType(),
				"message" => $error->getMessage(),
				"code" => $error->getCode(),
			);
		}
		$export["data"]["errors"] = $errors;
		if ($title = $this->getTranslateTitle()) {
			$export["data"]["title"] = $title;
		}
		return $export;
	}
	public function setSong(song $song) {
		$this->setData($song, "song");
	}
	public function setTranslateLyrics(array $lyrics) {
		$this->setData($lyrics, "lyrics");
		$translates = array();
		foreach ($lyrics as $lyric) {
			$translates[$lyric->panret] = $lyric->text;
		}
		$this->setDataForm($translates, "translates");
	}
	public function setTranslateTitle($title) {
		$this->setData($title, "translatetitle");
	}
	public function setTranslateLang(string $lang) {
		$this->setDataForm($lang, "lang");
	}
	protected function getSong() {
		return $this->getData("song");
	}
	protected function getTranslateLyrics() {
		return $this->getData("lyrics");
	}
	protected function getTranslateTitle() {
		return $this->getData("translatetitle");
	}
}
