<?php
namespace themes\musixmatch\views\contributes\songs;
use packages\base\{db, translator, packages, options};
use themes\musixmatch\{viewTrait, views\formTrait};
use packages\ghafiye\views\contributes\songs\Sync as parentView;

class Sync extends parentView {
	use viewTrait, formTrait;
	protected $song;
	public function __beforeLoad(){
		$this->song = $this->getSong();
		$this->setTitle(translator::trans("ghafiye.contribute.sync.song", array("title" => $this->song->title($this->song->lang))));
		$this->addBodyClass("article");
		$this->addBodyClass("contribute");
		$this->addBodyClass("contribute-sync");
	}
	protected function isLtr() {
		return !in_array($this->song->lang, array("ar", "fa", "dv", "he", "ps", "sd", "ur", "yi", "ug", "ku"));
	}
}
