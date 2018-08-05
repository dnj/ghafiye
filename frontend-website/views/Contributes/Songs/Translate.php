<?php
namespace themes\musixmatch\views\contributes\songs;
use packages\base\{db, translator, packages, options};
use themes\musixmatch\{viewTrait, views\formTrait};
use packages\ghafiye\{views\contributes\songs\Translate as parentView, song, song\lyric};

class Translate extends parentView {
	use viewTrait, formTrait;
	protected $song;
	protected $translates = array();
	protected $hasTranslate = true;
	public function __beforeLoad(){
		$this->song = $this->getSong();
		$this->setTitle(translator::trans("ghafiye.contribute.translate.song", array("title" => $this->song->title($this->song->lang))));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-translate");
	}
	protected function getLyrics(): array {
		$lyric = new lyric();
		$lyric->where("song", $this->song->id);
		$lyric->where("lang", $this->song->lang);
		$lyric->where("status", lyric::published);
		return $lyric->get();
	}
	protected function isLtr() {
		return !in_array($this->song->lang, array("ar", "fa", "dv", "he", "ps", "sd", "ur", "yi", "ug", "ku"));
	}
	protected function getTranslateProgress(string $lang): int {
		if (!$this->hasTranslate) {
			return 0;
		}
		if (!$this->translates) {
			$translate = new song\Translate();
			$translate->where("song", $this->song->id);
			$translate->where("lang", $this->song->lang, "!=");
			$this->translates = $translate->get();
			if (!$this->translates) {
				$this->hasTranslate = false;
				return 0;
			}
		}
		foreach ($this->translates as $translate) {
			if ($translate->lang == $lang) {
				return $translate->progress;
			}
		}
		return 0;
	}
	protected function getLangsForSelect(): array {
		$langs = [];
		foreach(["fa", "en"] as $lang){
			if ($this->song->lang == $lang) {
				continue;
			}
			$langs[] = array(
				"title" => translator::trans("translations.langs.{$lang}"),
				"value" => $lang,
				"data" => array(
					"progress" => $this->getTranslateProgress($lang),
				),
			);
		}
		foreach(translator::$allowlangs as $lang){
			if(in_array($lang, ["en", "fa"]) or $this->song->lang == $lang){
				continue;
			}
			$langs[] = array(
				"title" => translator::trans("translations.langs.{$lang}"),
				"value" => $lang,
				"data" => array(
					"progress" => $this->getTranslateProgress($lang),
				),
			);
		}
		return $langs;
	}
}
